<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\Households.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Mana\ClientBundle\Entity\Household;
use Mana\ClientBundle\Entity\Member;

/**
 * Description of HouseholdV1Head.
 *
 * @author George
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
        $manager->persist($member);
        $member2 = new Member();
        $member2->setFname('Added');
        $member2->setSname('Member');
        $member2->setInclude(true);
        $eth = $this->getReference('cau');
        $member2->setEthnicity($eth);
        $manager->persist($member2);

        $household = new Household();
        $household->setActive(true);
        $household->setDateAdded(new \DateTime('last year'));
        $household->setHead($member);
        $household->addMember($member);
        $household->addMember($member2);
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

        $manager->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
