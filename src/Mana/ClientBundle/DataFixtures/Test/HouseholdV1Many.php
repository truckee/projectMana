<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\HouseholdV1Many.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Mana\ClientBundle\Entity\Household;
use Mana\ClientBundle\Entity\Member;

/**
 * Description of HouseholdV1Head
 *
 * @author George
 */
class HouseholdV1Many extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $member = new Member();
        $member->setFname('MoreThanOne');
        $member->setSname('Member');
        $eth = $this->getReference('cau');
        $member->setEthnicity($eth);
        $manager->persist($member);
        $member2 = new Member();
        $member2->setFname('Added');
        $member2->setSname('Member');
        $eth = $this->getReference('cau');
        $member2->setEthnicity($eth);
        $manager->persist($member2);


        $household = new Household();
        $household->setHead($member);
        $household->addMember($member);
        $household->addMember($member2);
        $this->setReference('house', $household);
        $unk = $this->getReference('unk');
        $household->setFoodstamp($unk);
        $manager->persist($household);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }

}
