<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DataFixtures\Test\DisabledConstants.php

namespace Truckee\ProjectmanaBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * DisabledConstants
 *
 */
class DisabledConstants extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //Center
        $site = $this->getReference('kb');
        $site->setEnabled(false);
        $siteTahoe = $this->getReference('tahoe');
        $siteTahoe->setEnabled(false);
        $manager->persist($site);

        //Contactdesc
        $desc = $this->getReference('general');
        $desc->setEnabled(false);
        $manager->persist($desc);

        //County
        $county = $this->getReference('placer');
        $county->setEnabled(false);
        $manager->persist($county);

        //Housing
        $own = $this->getReference('own');
        $own->setEnabled(false);
        $manager->persist($own);

        //Income
        $income = $this->getReference('noIncome');
        $income->setEnabled(false);
        $manager->persist($income);

        //Notfoodstamp
        $notApplied = $this->getReference('notA');
        $notApplied->setEnabled(false);
        $manager->persist($notApplied);

        //Reason
        $housing = $this->getReference('cost');
        $housing->setEnabled(false);
        $manager->persist($housing);

        //State
        $state = $this->getReference('ca');
        $state->setEnabled(false);
        $manager->persist($state);



        $house = $this->getReference('house3');
        $house->setCenter($site);
        $manager->persist($house);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}
