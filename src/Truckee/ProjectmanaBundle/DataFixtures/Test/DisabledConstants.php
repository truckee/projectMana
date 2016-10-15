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
        $site->setEnabled(FALSE);
        $manager->persist($site);

        //ContactDesc
        $desc = $this->getReference('general');
        $desc->setEnabled(FALSE);
        $manager->persist($desc);

        //County
        $county = $this->getReference('nevada');
        $county->setEnabled(FALSE);
        $manager->persist($county);

        //FsStatus
        $fsStatus = $this->getReference('unk');
        $fsStatus->setEnabled(FALSE);
        $manager->persist($fsStatus);

        //Housing
        $own = $this->getReference('own');
        $own->setEnabled(FALSE);
        $manager->persist($own);

        //Income
        $income = $this->getReference('noIncome');
        $income->setEnabled(FALSE);
        $manager->persist($income);

        //Notfoodstamp
        $notApplied = $this->getReference('notA');
        $notApplied->setEnabled(FALSE);
        $manager->persist($notApplied);

        //Reason
        $housing = $this->getReference('cost');
        $housing->setEnabled(FALSE);
        $manager->persist($housing);

        //State
        $state = $this->getReference('ca');
        $state->setEnabled(FALSE);
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
