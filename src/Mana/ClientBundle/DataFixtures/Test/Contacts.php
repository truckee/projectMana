<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\Contacts.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mana\ClientBundle\Entity\Contact;

/**
 * Contacts.
 */
class Contacts extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $siteTahoe = $this->getReference('tahoe');
        $siteTruckee = $this->getReference('truckee');
        $countyPlacer = $this->getReference('placer');
        $countyNevada = $this->getReference('nevada');
        $descGeneral = $this->getReference('general');
        $descFACE = $this->getReference('face');
        $house1 = $this->getReference('house1');
        $house2 = $this->getReference('house2');

        $date = new \DateTime('last month');
        $contact = new Contact();
        $contact->setCenter($siteTahoe);
        $contact->setCounty($countyPlacer);
        $contact->setContactDate($date);
        $contact->setContactDesc($descGeneral);
        $contact->setHousehold($house1);
        $this->setReference('contact', $contact);
        $manager->persist($contact);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
