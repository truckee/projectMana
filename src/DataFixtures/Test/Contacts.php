<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\DataFixtures\Test\Contacts.php

namespace App\DataFixtures\Test;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Contact;

/**
 * Contacts.
 */
class Contacts extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $siteTahoe = $this->getReference('tahoe');
        $siteTruckee = $this->getReference('truckee');
        $siteIncline = $this->getReference('incline');
        $countyPlacer = $this->getReference('placer');
        $countyNevada = $this->getReference('nevada');
        $countyWashoe = $this->getReference('washoe');
        $descGeneral = $this->getReference('general');
        $descFACE = $this->getReference('face');
        $house1 = $this->getReference('house1');
        $house3 = $this->getReference('house3');

        $date = new \DateTime('last month');
        $contact = new Contact();
        $contact->setCenter($siteTahoe);
        $contact->setContactDate($date);
        $contact->setContactdesc($descGeneral);
        $contact->setHousehold($house1);
        $contact->setCounty($countyPlacer);
        $house1->addContact($contact);
        $this->setReference('contact', $contact);
        $manager->persist($contact);
        $manager->persist($house1);

        $contact1 = new Contact();
        $contact1->setCenter($siteTahoe);
        $contact1->setContactDate($date);
        $contact1->setContactdesc($descGeneral);
        $contact1->setHousehold($house1);
        $contact1->setCounty($this->getReference('placer'));
        $this->setReference('contact1', $contact1);
        $house3->addContact($contact1);
        $manager->persist($contact1);
        $manager->persist($house3);

        $contact2 = new Contact();
        $contact2->setCenter($siteTahoe);
        $contact2->setContactDate($date);
        $contact2->setContactdesc($descGeneral);
        $contact2->setHousehold($house1);
        $contact2->setCounty($countyNevada);
        $this->setReference('contact2', $contact2);
        $house3->addContact($contact2);
        $manager->persist($contact2);
        $manager->persist($house3);

        $contact3 = new Contact();
        $contact3->setCenter($siteIncline);
        $contact3->setContactDate($date);
        $contact3->setContactdesc($descGeneral);
        $contact3->setHousehold($house1);
        $contact3->setCounty($countyWashoe);
        $this->setReference('contact3', $contact3);
        $house3->addContact($contact3);
        $manager->persist($contact3);
        $manager->persist($house3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
