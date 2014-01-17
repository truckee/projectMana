<?php

//src/Mana/ClientBundle/Utilities/Searches.php

namespace Mana\ClientBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * returns set of households whose heads are possible matches for new head
 */
class Searches {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * Get households whose heads match input
     * @param type $qtext
     * @return array
     */
    public function getMatches($qtext) {
        $name = explode(' ', $qtext);
        if (empty($qtext) || !array_key_exists(1, $name)) {
            return false;
        }

        $name = explode(' ', $qtext);
        $incoming['fname'] = $name[0];
        $incoming['sname'] = $name[1];
        $fname1 = '%' . $incoming['fname'] . '%';
        $sname1 = $incoming['sname'];
        $fname2 = $incoming['fname'];
        $sname2 = '%' . $incoming['sname'] . '%';

        $fname1 = '%' . $incoming['fname'] . '%';
        $sname1 = $incoming['sname'];
        $fname2 = $incoming['fname'];
        $sname2 = '%' . $incoming['sname'] . '%';

        $sql = "select h from ManaClientBundle:Household h 
            join ManaClientBundle:Member c WITH h.hohId = c.id
            where (c.fname like :fname1 and soundex(c.sname) = soundex(:sname1))
            or
            (soundex(c.fname) = soundex(:fname2) and c.sname like :sname2)
            order by c.sname, c.fname";
        $query = $this->em->createQuery($sql)
                ->setParameters(array(
            'fname1' => $fname1,
            'sname1' => $sname1,
            'fname2' => $fname2,
            'sname2' => $sname2,
        ));

        return $query->getResult();
    }

    public function getLatest() {
        $latestContacts = array();
        $query = $this->em->createQuery("select r.id from ManaClientBundle:Center r");
        $idArray = $query->getResult();
        //for each center get the most recent date of distribution
        //then get the contacts for that center and date
        foreach ($idArray as $arr) {
            $sqlDate = "select c.contactDate from ManaClientBundle:Contact c
                join c.center r
                where c.center = :center
                and r.enabled = 1
                order by c.contactDate desc";
            $query = $this->em->createQuery($sqlDate)
                    ->setMaxResults(1)
                    ->setParameter('center', $arr['id']);
            $date = $query->getResult();
            if (!empty($date)) {
                $latest[$arr['id']] = date_format($date[0]['contactDate'], 'Y-m-d');

                //get contacts for center and date
                $sql = "select c from ManaClientBundle:Contact c 
                where c.contactDate = :latest and c.center = :center
                and c.household is not null";
                $contacts = $this->em->createQuery($sql)
                        ->setParameters(array(
                            'center' => $arr['id'],
                            'latest' => $latest[$arr['id']]))
                        ->getResult();

                foreach ($contacts as $contact) {
                    $latestContacts[] = $contact;
                }
            } else {
                $contact = array(
                    'center' => $arr['id'],
                    'latest' => null
                );
                $latestContacts[] = $contact;
            }
        }
        $i = 0;
        $contactSet = array();
        foreach ($latestContacts as $latest) {
            if (is_object($latest)) {
                $id = $latest->getHousehold()->getId();
                $contactSet[$id]['id'] = $latest->getHousehold()->getId();
                $contactSet[$id]['centerId'] = $latest->getCenter()->getId();
                $contactSet[$id]['center'] = $latest->getCenter()->getCenter();
                $contactSet[$id]['head'] = $latest->getHousehold()->getHead()->getSname();
                $contactSet[$id]['head'] .= ', ';
                $contactSet[$id]['head'] .= $latest->getHousehold()->getHead()->getFname();
                $contactSet[$id]['dob'] = $latest->getHousehold()->getHead()->getDob();
                $contactSet[$id]['type'] = $latest->getContactDesc()->getContactDesc();
                $contactSet[$id]['date'] = date_format($latest->getContactDate(), 'm/d/Y');
                $i++;
            }
        }
//        $members = array();
//        foreach ($contactSet as $key => $row) {
//            $members[$key] = $row['head'];
//        }
//        array_multisort($members, SORT_ASC, $contactSet);
        return $contactSet;
    }

    public function getRoster($centerId) {
        $latestContacts = array();
        $sqlDate = "select c.contactDate from ManaClientBundle:Contact c
                join c.center r
                where c.center = :center
                and r.enabled = 1
                order by c.contactDate desc";
        $query = $this->em->createQuery($sqlDate)
                ->setMaxResults(1)
                ->setParameter('center', $centerId);
        $date = $query->getResult();
        if (empty($date)) {
            return null;
        }
        $latestDate = date_format($date[0]['contactDate'], 'Y-m-d');

        //get contacts for center and date
        $sql = "select c from ManaClientBundle:Contact c 
                join c.center r
                where c.contactDate = :latest and c.center = :center
                and r.enabled = 1
                and c.household is not null";
        $contacts = $this->em->createQuery($sql)
                ->setParameters(array(
                    'center' => $centerId,
                    'latest' => $latestDate))
                ->getResult();

        foreach ($contacts as $contact) {
            $latestContacts[] = $contact;
        }

        $contactSet = array();
        foreach ($latestContacts as $latest) {
            $contactSet[] = array(
                'id' => $latest->getHousehold()->getId(),
                'head' => $latest->getHousehold()->getHead()->getSname()
                . ', ' . $latest->getHousehold()->getHead()->getFname(),
                'dob' => $latest->getHousehold()->getHead()->getDob(),
            );
        }
        $members = array();
        foreach ($contactSet as $key => $row) {
            $members[$key] = $row['head'];
        }
        array_multisort($members, SORT_ASC, $contactSet);
//$price = array();
//foreach ($inventory as $key => $row)
//{
//    $price[$key] = $row['price'];
//}
//array_multisort($price, SORT_DESC, $inventory);
        return array(
            'latestDate' => $latestDate,
            'contactSet' => $contactSet);
    }

    /**
     * get members that match input name
     * @param type $qtext
     * @return array
     */
    public function getMembers($qtext) {
        $length = strpos($qtext, ' ');
        if (empty($length)) {
            return null;
        }
        $incoming['fname'] = substr($qtext, 0, $length);
        $incoming['sname'] = substr($qtext, $length + 1);
        $fname1 = '%' . $incoming['fname'] . '%';
        $sname1 = $incoming['sname'];
        $fname2 = $incoming['fname'];
        $sname2 = '%' . $incoming['sname'] . '%';

        $fname1 = '%' . $incoming['fname'] . '%';
        $sname1 = $incoming['sname'];
        $fname2 = $incoming['fname'];
        $sname2 = '%' . $incoming['sname'] . '%';

        $sql = "select c from ManaClientBundle:Member c 
            where (c.fname like :fname1 and soundex(c.sname) = soundex(:sname1))
            or
            (soundex(c.fname) = soundex(:fname2) and c.sname like :sname2)
            order by c.sname, c.fname";
        $query = $this->em->createQuery($sql)
                ->setParameters(array(
            'fname1' => $fname1,
            'sname1' => $sname1,
            'fname2' => $fname2,
            'sname2' => $sname2,
        ));
        $found = $query->getResult();

        return $found;
    }

}
