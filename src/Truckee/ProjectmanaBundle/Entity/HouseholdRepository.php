<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class HouseholdRepository extends EntityRepository
{
    /**
     * Set initial values for household entity.
     *
     * @param object $household
     * @param object $member
     * @param object $session
     *
     * @return int
     */
    public function initialize($household, $member, $session = null)
    {
        $em = $this->getEntityManager();
        //phone added here because it's configured for 1:n but used as 1:1
        $phone = new Phone();
        $household->addPhone($phone);
        $household->setActive(1);
        $household->setDateAdded(new \DateTime());
        $member->setInclude(1);
        $relation = $em->getRepository('TruckeeProjectmanaBundle:Relationship')->findOneBy(['relation' => 'Self']);
        $member->setRelation($relation);
        $foodstamp = $household->getFoodstamp();
        if (empty($foodstamp)) {
            $unk = $em->getRepository('TruckeeProjectmanaBundle:FsStatus')->findOneBy(['status' => 'Unknown']);
            $household->setFoodstamp($unk);
        }
        //if from match results, add & set head of household
        if (count($household->getMembers()) == 0) {
            $household->addMember($member);
            $household->setHead($member);
            $session->set('household', '');
            $session->set('member', '');
        }
        $em->persist($household);
        $em->flush();
        $id = $household->getId();

        return $id;
    }

    /**
     * Add contact to set of households
     *
     * @param array $households
     * @param array $contactData
     */
    public function addContacts($households, $contactData)
    {
        $em = $this->getEntityManager();
        foreach ($households as $id) {
            $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
            $houseContacts = $household->getContacts();
            $nContacts = count($houseContacts);
            $first = ($nContacts > 0) ? 0 : 1;
            $contact = new Contact();
            $contact->setContactDate($contactData['date']);
            $contact->setCenter($contactData['center']);
            $contact->setContactDesc($contactData['desc']);
//            $contact->setCounty($contactData['center']->getCounty());
            $contact->setFirst($first);
            $household->addContact($contact);
            $em->persist($household);
        }
        $em->flush();
    }
    
//    select distinct r.center, concat(sname, ', ', fname) as Head, c.household_id ID, 
//    date_format(dob, '%m/%d/%Y') as DOB, if(max(contact_date) < '2017-07-01', 'Yes', 'No') 'Form Req\'d' from household h
//    join contact c on c.household_id = h.id
//    join center r on r.id = c.center_id
//    join member m on m.id = h.hoh_id
//    where c.contact_date >= '2017-01-01'
//    group by Head
//    order by center, Head;

    /**
     * Annual turkey report
     */
    public function annualTurkey()
    {
        $jan1 = new \DateTime('first day of January');
        $jul1 = new \DateTime('first day of July');

        return $this->getEntityManager()->createQueryBuilder()
            ->select('m.sname', 'm.fname', 'm.dob', 'm.id', 'r.center', 'CASE WHEN MAX(c.contactDate) <= :jul1 THEN \'Yes\' ELSE \'No\' END Form')
            ->distinct(true)
            ->from('TruckeeProjectmanaBundle:Household', 'h')
            ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.household = h')
            ->join('TruckeeProjectmanaBundle:Center', 'r', 'WITH', 'c.center = r')
            ->join('TruckeeProjectmanaBundle:Member', 'm', 'WITH', 'h.head = m')
            ->where('c.contactDate >= :jan1')
            ->groupBy('m.sname')
            ->addGroupBy('m.fname')
            ->orderBy('m.sname')
            ->addOrderBy('m.fname')
            ->setParameter('jan1', $jan1)
            ->setParameter('jul1', $jul1)
            ->getQuery()
            ->getResult();
    }
}
