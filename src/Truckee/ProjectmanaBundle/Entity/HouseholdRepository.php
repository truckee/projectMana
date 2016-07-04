<?php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class HouseholdRepository extends EntityRepository
{
    /**
     * Set initial values for household entity.
     * 
     * @param type $household
     * @param type $member
     * @param type $session
     *
     * @return mixed
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
            $contact->setCounty($contactData['center']->getCounty());
            $contact->setFirst($first);
            $household->addContact($contact);
            $em->persist($household);
        }
        $em->flush();
    }
}
