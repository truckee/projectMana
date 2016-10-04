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

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
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
        $manager->persist($member);

        $member2 = new Member();
        $member2->setFname('Added');
        $member2->setSname('Member');
        $dob = new \DateTime('4/12/1990');
        $member2->setDob($dob);
        $member2->setSex('Male');
        $member2->setInclude(true);
        $member2->setEthnicity($eth);
        $this->setReference('member2', $member2);
        $manager->persist($member2);

        $household = new Household();
        $household->setActive(true);
        $household->setDateAdded(new \DateTime('last year'));
        $household->setHead($member);
        $household->addMember($member);
        $household->addMember($member2);
        $household->setFoodstamp(1);
        $this->setReference('house1', $household);
        $unk = $this->getReference('unk');
        $household->setFoodstamp($unk);
        $manager->persist($household);

        $member3 = new Member();
        $member3->setFname('Single');
        $member3->setSname('Head');
        $member3->setInclude(true);
        $eth = $this->getReference('cau');
        $member3->setEthnicity($eth);
        $this->setReference('member3', $member3);
        $manager->persist($member3);

        $household2 = new Household();
        $household2->setActive(true);
        $household2->setDateAdded(new \DateTime('last month'));
        $household2->setHead($member3);
        $household2->addMember($member3);
        $this->setReference('house2', $household2);
        $unk = $this->getReference('unk');
        $household2->setFoodstamp($unk);
        $manager->persist($household2);

        $member4 = new Member();
        $member4->setFname('Some');
        $member4->setSname('Person');
        $member4->setInclude(true);
        $eth = $this->getReference('cau');
        $member4->setEthnicity($eth);
        $this->setReference('member4', $member4);
        $manager->persist($member4);

        $household3 = new Household();
        $household3->setActive(true);
        $household3->setDateAdded(new \DateTime('last month'));
        $household3->setHead($member4);
        $household3->addMember($member4);
        $this->setReference('house3', $household3);
        $unk = $this->getReference('unk');
        $household3->setFoodstamp($unk);
        $manager->persist($household3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
