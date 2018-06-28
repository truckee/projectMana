<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DataFixtures\Test\Households.php

namespace Truckee\ProjectmanaBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Truckee\ProjectmanaBundle\Entity\Address;
use Truckee\ProjectmanaBundle\Entity\Household;
use Truckee\ProjectmanaBundle\Entity\Member;

/**
 * Households.
 *
 * @author George Brooks
 */
class Households extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $member = new Member();
        $member->setFname('MoreThanOne');
        $member->setSname('Member');
        $member->setInclude(true);
        $eth = $this->getReference('cau');
        $member->setEthnicity($eth);
        $this->setReference('member', $member);

        $member2 = new Member();
        $member2->setFname('Added');
        $member2->setSname('Member');
        $dob = new \DateTime('4/12/1990');
        $member2->setDob($dob);
        $member2->setSex('Male');
        $member2->setInclude(true);
        $member2->setEthnicity($eth);
        $this->setReference('member2', $member2);

        $household = new Household();
        $household->setActive(true);
        $household->setDateAdded(new \DateTime('last year'));
        $household->setHead($member);
        $household->addMember($member);
        $household->addMember($member2);
        $household->setFoodstamp(1);
        $this->setReference('house1', $household);
        $fsAppl = $this->getReference('fsApplied');
        $household->setFoodstamp($fsAppl);

        $member3 = new Member();
        $member3->setFname('Single');
        $member3->setSname('Head');
        $member3->setInclude(true);
        $eth = $this->getReference('cau');
        $member3->setEthnicity($eth);
        $this->setReference('member3', $member3);

        $household2 = new Household();
        $household2->setActive(true);
        $household2->setDateAdded(new \DateTime('last month'));
        $household2->setHead($member3);
        $household2->addMember($member3);
        $this->setReference('house2', $household2);
        $household2->setFoodstamp($fsAppl);

        $member4 = new Member();
        $member4->setFname('Some');
        $member4->setSname('Person');
        $member4->setInclude(true);
        $eth = $this->getReference('cau');
        $member4->setEthnicity($eth);
        $this->setReference('member4', $member4);

        $household3 = new Household();
        $household3->setActive(true);
        $household3->setDateAdded(new \DateTime('last month'));
        $household3->setHead($member4);
        $household3->addMember($member4);
        //set household properties for disabled tests
        $unk = $this->getReference('unk');
        $household3->setFoodstamp($unk);
        $site = $this->getReference('kb');
        $household3->setCenter($site);
        $site->addHousehold($household3);
        $housing = $this->getReference('own');
        $housing->addHousehold($household3);
        $household3->setHousing($housing);
        $income = $this->getReference('noIncome');
        $income->addHousehold($household3);
        $household3->setIncome($income);
        $notA = $this->getReference('notA');
        $notA->addHousehold($household3);
        $household3->setNotfoodstamp($notA);
        $reason = $this->getReference('cost');
        $household3->addReason($reason);
        $this->setReference('house3', $household3);

        $address = new Address();
        $address->setAddresstype($this->getReference('mailing'));
        $address->setLine1('123 Some St.');
        $address->setCity('City');
        $ca = $this->getReference('ca');
        $address->setState($ca);
        $address->setZip('88888');

//        $phone = new Phone();
//        $phone->setAreacode('123');
//        $phone->setPhoneNumber('123-4567');

        $house = $this->getReference('house1');
        $house->addAddress($address);
//        $house->addPhone($phone);
        $house->setAreacode('123');
        $house->setPhoneNumber('123-4567');
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
        $reason = $this->getReference('cost');
        $house->addReason($reason);

        $house2 = $this->getReference('house2');
        $fsNo = $this->getReference('fsNo');
        $fsNo->addHousehold($house2);
        $truckee = $this->getReference('truckee');
        $rent = $this->getReference('rent');
        $house2->setHousing($rent);
        $house2->setCenter($truckee);

        $manager->persist($member);
        $manager->persist($house);
        $manager->persist($house2);
        $manager->persist($member2);
        $manager->persist($household);
        $manager->persist($member3);
        $manager->persist($household2);
        $manager->persist($member4);
        $manager->persist($household3);
        $manager->persist($address);
        $manager->persist($site);
        $manager->persist($housing);
        $manager->persist($income);
        $manager->persist($notA);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
