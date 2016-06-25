<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\HouseholdOptons.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Mana\ClientBundle\Entity\Address;
use Mana\ClientBundle\Entity\Phone;

/**
 * HouseholdOptons.
 */
class HouseholdOptions extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $address = new Address();
        $address->setAddresstype($this->getReference('mailing'));
        $address->setLine1('123 Some St.');
        $address->setCity('City');
        $ca = $this->getReference('ca');
        $address->setState($ca);
        $address->setZip('88888');
        $manager->persist($address);

        $phone = new Phone();
        $phone->setAreacode('123');
        $phone->setPhoneNumber('123-4567');

        $house = $this->getReference('house1');
        $house->addAddress($address);
        $house->addPhone($phone);
        $fsNo = $this->getReference('fsNo');
        $fsNo->addHousehold($house);
        $fsa = $this->getReference('fsamount1');
        $fsa->addHousehold($house);
        $house->setFsamount($fsa);
        $hse = $this->getReference('rent');
        $hse->addHousehold($house);
        $house->setHousing($hse);
        $income = $this->getReference('lowIncome');
        $income->addHousehold($house);
        $house->setIncome($income);
        $fs = $this->getReference('notQ');
        $fs->addHousehold($house);
        $reason = $this->getReference('housing');
        $house->addReason($reason);
        $manager->persist($house);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6; // the order in which fixtures will be loaded
    }
}
