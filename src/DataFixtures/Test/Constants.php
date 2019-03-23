<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures\Test;

use App\Entity\AddressType;
use App\Entity\Assistance;
use App\Entity\Center;
use App\Entity\Contactdesc;
use App\Entity\County;
use App\Entity\Ethnicity;
use App\Entity\Housing;
use App\Entity\Income;
use App\Entity\Notfoodstamp;
use App\Entity\Organization;
use App\Entity\Reason;
use App\Entity\Relationship;
use App\Entity\State;
use App\Entity\Work;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Set of Constants required by tests.
 *
 * @author George Brooks
 */
class Constants extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $svc = new Assistance();
        $svc->setAssistance('Other');
        $svc->setEnabled(true);
        $manager->persist($svc);

        $org = new Organization();
        $org->setOrganization('Other');
        $org->setEnabled(true);
        $manager->persist($org);

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
        $this->setReference('incline', $center3);
        $washoe = $this->getReference('washoe');
        $center3->setCounty($washoe);
        $center3->setEnabled(true);
        $manager->persist($center3);

        $desc = new Contactdesc();
        $desc->setContactdesc('FACE');
        $desc->setEnabled(true);
        $this->setReference('face', $desc);
        $manager->persist($desc);

        $desc1 = new Contactdesc();
        $desc1->setContactdesc('General Dist.');
        $desc1->setEnabled(true);
        $this->setReference('general', $desc1);
        $manager->persist($desc1);

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
        $work->setJob('work');
        $this->setReference('work', $work);
        $manager->persist($work);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
