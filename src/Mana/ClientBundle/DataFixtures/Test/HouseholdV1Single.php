<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\HouseholdV1Single.php

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
class HouseholdV1Single extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $member = new Member();
        $member->setFname('Bogus');
        $member->setSname('Beelzebub');
        $eth = $this->getReference('cau');
        $member->setEthnicity($eth);
        $manager->persist($member);
        $household = new Household();
        $household->setHead($member);
        $household->addMember($member);
        $this->setReference('house', $household);
        $unk = $this->getReference('unk');
        $household->setFoodstamp($unk);
        $manager->persist($household);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

}
