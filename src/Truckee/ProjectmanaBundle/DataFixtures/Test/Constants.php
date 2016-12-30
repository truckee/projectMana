<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Truckee\ProjectmanaBundle\Entity\AddressType;
use Truckee\ProjectmanaBundle\Entity\Center;
use Truckee\ProjectmanaBundle\Entity\ContactDesc;
use Truckee\ProjectmanaBundle\Entity\County;
use Truckee\ProjectmanaBundle\Entity\Ethnicity;
use Truckee\ProjectmanaBundle\Entity\FsAmount;
use Truckee\ProjectmanaBundle\Entity\FsStatus;
use Truckee\ProjectmanaBundle\Entity\Housing;
use Truckee\ProjectmanaBundle\Entity\Income;
use Truckee\ProjectmanaBundle\Entity\Notfoodstamp;
use Truckee\ProjectmanaBundle\Entity\Reason;
use Truckee\ProjectmanaBundle\Entity\Relationship;
use Truckee\ProjectmanaBundle\Entity\State;
use Truckee\ProjectmanaBundle\Entity\Work;

/**
 * Set of Constants required by tests.
 *
 * @author George Brooks
 */
class Constants extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $eth = new Ethnicity();
        $eth->setEthnicity('Caucasian');
        $this->setReference('cau', $eth);
        $eth->setAbbreviation('Cau');
        $eth->setEnabled(true);
        $manager->persist($eth);

        $eth1 = new Ethnicity();
        $eth1->setEthnicity('African-American');
        $eth1->setAbbreviation('AfrAm');
        $eth1->setEnabled(true);
        $manager->persist($eth1);

        $eth2 = new Ethnicity();
        $eth2->setEthnicity('Asian');
        $eth2->setAbbreviation('Asian');
        $eth2->setEnabled(true);
        $manager->persist($eth2);

        $eth3 = new Ethnicity();
        $eth3->setEthnicity('Hispanic');
        $eth3->setAbbreviation('Hisp');
        $eth3->setEnabled(true);
        $manager->persist($eth3);

        $eth4 = new Ethnicity();
        $eth4->setEthnicity('Native American');
        $eth4->setAbbreviation('NtvAm');
        $eth4->setEnabled(true);
        $manager->persist($eth4);

        $eth5 = new Ethnicity();
        $eth5->setEthnicity('Hawaiian/Pacific Islander');
        $eth5->setAbbreviation('HaPI');
        $eth5->setEnabled(true);
        $manager->persist($eth5);

        $eth6 = new Ethnicity();
        $eth6->setEthnicity('Other');
        $eth6->setAbbreviation('Other');
        $eth6->setEnabled(true);
        $manager->persist($eth6);

        $eth7 = new Ethnicity();
        $eth7->setEthnicity('Unknown');
        $eth7->setAbbreviation('Unk');
        $eth7->setEnabled(true);
        $manager->persist($eth7);

        $county = new County();
        $county->setCounty('Placer');
        $this->setReference('placer', $county);
        $county->setEnabled(true);
        $manager->persist($county);

        $county1 = new County();
        $county1->setCounty('Nevada');
        $this->setReference('nevada', $county1);
        $county1->setEnabled(true);
        $manager->persist($county1);

        $county2 = new County();
        $county2->setCounty('Washoe');
        $this->setReference('washoe', $county2);
        $county2->setEnabled(true);
        $manager->persist($county2);

        $center = new Center();
        $center->setCenter('Tahoe City');
        $placer = $this->getReference('placer');
        $center->setCounty($placer);
        $center->setEnabled(true);
        $this->setReference('tahoe', $center);
        $manager->persist($center);

        $center1 = new Center();
        $center1->setCenter('Kings Beach');
        $center1->setCounty($placer);
        $center1->setEnabled(true);
        $this->setReference('kb', $center1);
        $manager->persist($center1);

        $center2 = new Center();
        $center2->setCenter('Truckee');
        $nevada = $this->getReference('nevada');
        $center2->setCounty($nevada);
        $center2->setEnabled(true);
        $this->setReference('truckee', $center2);
        $manager->persist($center2);

        $center3 = new Center();
        $center3->setCenter('Incline Village');
        $washoe = $this->getReference('washoe');
        $center3->setCounty($washoe);
        $center3->setEnabled(true);
        $manager->persist($center3);

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('No');
        $foodstamp->setEnabled(true);
        $this->setReference('fsNo', $foodstamp);
        $manager->persist($foodstamp);

        $foodstamp1 = new FsStatus();
        $foodstamp1->setStatus('Yes');
        $foodstamp1->setEnabled(true);
        $manager->persist($foodstamp1);

        $foodstamp2 = new FsStatus();
        $foodstamp2->setStatus('Appl');
        $foodstamp2->setEnabled(true);
        $this->setReference('fsApplied', $foodstamp2);
        $manager->persist($foodstamp2);

        $foodstamp3 = new FsStatus();
        $foodstamp3->setStatus('Unknown');
        $foodstamp3->setEnabled(true);
        $this->setReference('unk', $foodstamp3);
        $manager->persist($foodstamp3);

        $desc = new ContactDesc();
        $desc->setContactDesc('FACE');
        $desc->setEnabled(true);
        $this->setReference('face', $desc);
        $manager->persist($desc);

        $desc1 = new ContactDesc();
        $desc1->setContactDesc('General Dist.');
        $desc1->setEnabled(true);
        $this->setReference('general', $desc1);
        $manager->persist($desc1);

        $fsamount1 = new FsAmount();
        $fsamount1->setAmount('0 - 200');
        $fsamount1->setEnabled(true);
        $this->setReference('fsamount1', $fsamount1);
        $manager->persist($fsamount1);

        $fsamount2 = new FsAmount();
        $fsamount2->setAmount('201 - 400');
        $fsamount2->setEnabled(true);
        $this->setReference('fsamount2', $fsamount2);
        $manager->persist($fsamount2);

        $addType1 = new AddressType();
        $addType1->setAddresstype('Mailing');
        $addType1->setEnabled(true);
        $this->setReference('mailing', $addType1);
        $manager->persist($addType1);

        $addType2 = new AddressType();
        $addType2->setAddresstype('Physical');
        $addType2->setEnabled(true);
        $this->setReference('physical', $addType2);
        $manager->persist($addType2);

        $ca = new State();
        $ca->setState('CA');
        $ca->setEnabled(true);
        $this->setReference('ca', $ca);
        $manager->persist($ca);

        $nv = new State();
        $nv->setState('NV');
        $nv->setEnabled(true);
        $this->setReference('nv', $nv);
        $manager->persist($nv);

        $notQualified = new Notfoodstamp();
        $notQualified->setNotfoodstamp('Not qualified');
        $notQualified->setEnabled(true);
        $this->setReference('notQ', $notQualified);
        $manager->persist($notQualified);

        $notApplied = new Notfoodstamp();
        $notApplied->setNotfoodstamp('Not applied');
        $notApplied->setEnabled(true);
        $this->setReference('notA', $notApplied);
        $manager->persist($notApplied);

        $rent = new Housing();
        $rent->setHousing('Renting');
        $rent->setEnabled(true);
        $this->setReference('rent', $rent);
        $manager->persist($rent);

        $own = new Housing();
        $own->setHousing('Owner');
        $own->setEnabled(true);
        $this->setReference('own', $own);
        $manager->persist($own);

        $cost = new Reason();
        $cost->setReason('Housing/Utility Cost');
        $cost->setEnabled(true);
        $this->setReference('cost', $cost);
        $manager->persist($cost);

        $unemployed = new Reason();
        $unemployed->setReason('Unemployed');
        $unemployed->setEnabled(true);
        $this->setReference('unemployed', $unemployed);
        $manager->persist($unemployed);

        $income1 = new Income();
        $income1->setIncome('0 - 500');
        $income1->setEnabled(true);
        $this->setReference('lowIncome', $income1);
        $manager->persist($income1);

        $income2 = new Income();
        $income2->setIncome('501 - 1,000');
        $income2->setEnabled(true);
        $this->setReference('medIncome', $income2);
        $manager->persist($income2);

        $income3 = new Income();
        $income3->setIncome('0 - 0');
        $income3->setEnabled(true);
        $this->setReference('noIncome', $income3);
        $manager->persist($income3);

        $relation = new Relationship();
        $relation->setRelation('related');
        $relation->setEnabled(true);
        $this->setReference('related', $relation);
        $manager->persist($relation);

        $work = new Work();
        $work->setEnabled(true);
        $work->setWork('work');
        $this->setReference('work', $work);
        $manager->persist($work);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
