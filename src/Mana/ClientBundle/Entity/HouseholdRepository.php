<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

class HouseholdRepository extends EntityRepository
{

    /**
     * Set initial values for household entity
     * 
     * @param type $household
     * @param type $member
     * @param type $session
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
        $relation = $em->getRepository('ManaClientBundle:Relationship')->find(1);
        $member->setRelation($relation);
        $foodstamp = $household->getFoodstamp();
        if (empty($foodstamp)){
            $unk = $em->getRepository("ManaClientBundle:FsStatus")->findOneBy(['status' => 'Unknown']);
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
            $household = $em->getRepository('ManaClientBundle:Household')->find($id);
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

    /**
     * Household version flags:
     * 0: version 2
     * 1: v1, single member
     * >1: v1, more than 1 member
     * @param type $id
     */
    public function getHouseholdVersionFlag($id)
    {
        $em = $this->getEntityManager();
        $size = $em->createQuery(
                        "select count(m.fname) size from ManaClientBundle:Member m "
                        . "where m.household =  $id")
                ->getSingleScalarResult();
        $dob = $em->createQuery(
                        " select m.dob from  ManaClientBundle:Member m "
                        . "join ManaClientBundle:Household h with h.head = m "
                        . "where h.id =  $id")
                ->getSingleScalarResult();
        return (!empty($dob)) ? 0 : $size;
    }

}
