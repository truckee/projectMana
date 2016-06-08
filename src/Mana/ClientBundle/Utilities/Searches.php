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
        $string = trim($qtext);
        $length = strpos($qtext, ' ');
        if (empty($length)) {
            return null;
        }
        $name = addslashes($string);
//        $name = preg_replace("/[^-'A-Za-z0-9 ]/", '', $string);
        $conn = $this->em->getConnection();
        $sql = "select m.id from member m where match(m.fname, m.sname) against (quote('$name'))";
        $stmt = $conn->query($sql);
        $members = $stmt->fetchAll();
        $found = array();
        foreach ($members as $member) {
            $found[] = $this->em->getRepository('ManaClientBundle:Member')->find($member['id']);
        }
        
        return $found;
    }

}
