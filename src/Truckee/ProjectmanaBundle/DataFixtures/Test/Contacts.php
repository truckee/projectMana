<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DataFixtures\Test\Contacts.php

namespace Truckee\ProjectmanaBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Truckee\ProjectmanaBundle\Entity\Contact;

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
        $house3 = $this->getReference('house3');

        $date = new \DateTime('last month');
        $contact = new Contact();
        $contact->setCenter($siteTahoe);
        $contact->setCounty($countyPlacer);
        $contact->setContactDate($date);
        $contact->setContactDesc($descGeneral);
        $contact->setHousehold($house1);
        $house1->addContact($contact);
        $this->setReference('contact', $contact);
        $manager->persist($contact);
        $manager->persist($house1);

        $contact1 = new Contact();
        $contact1->setCenter($siteTahoe);
        $contact1->setCounty($countyPlacer);
        $contact1->setContactDate($date);
        $contact1->setContactDesc($descGeneral);
        $contact1->setHousehold($house1);
        $this->setReference('contact1', $contact1);
        $house3->addContact($contact1);
        $this->setReference('contact', $contact1);
        $manager->persist($contact1);
        $manager->persist($house3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
