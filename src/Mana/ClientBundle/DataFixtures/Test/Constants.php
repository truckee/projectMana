<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mana\ClientBundle\Entity\Ethnicity;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Entity\County;
use Mana\ClientBundle\Entity\FsStatus;

/**
 * Description of Constants.
 *
 * @author George
 */
class Constants extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
        $county->setEnabled(1);
        $manager->persist($county);

        $county1 = new County();
        $county1->setCounty('Nevada');
        $this->setReference('nevada', $county1);
        $county1->setEnabled(1);
        $manager->persist($county1);

        $county2 = new County();
        $county2->setCounty('Washoe');
        $this->setReference('washoe', $county2);
        $county2->setEnabled(1);
        $manager->persist($county2);

        $center = new Center();
        $center->setCenter('Tahoe City');
        $placer = $this->getReference('placer');
        $center->setCounty($placer);
        $center->setEnabled(1);
        $this->setReference('tahoe', $center);
        $manager->persist($center);

        $center1 = new Center();
        $center1->setCenter('Kings Beach');
        $center1->setCounty($placer);
        $center1->setEnabled(1);
        $manager->persist($center1);

        $center2 = new Center();
        $center2->setCenter('Truckee');
        $nevada = $this->getReference('nevada');
        $center2->setCounty($nevada);
        $center2->setEnabled(1);
        $manager->persist($center2);

        $center3 = new Center();
        $center3->setCenter('Incline Village');
        $washoe = $this->getReference('washoe');
        $center3->setCounty($washoe);
        $center3->setEnabled(1);
        $manager->persist($center3);

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('No');
        $manager->persist($foodstamp);

        $foodstamp1 = new FsStatus();
        $foodstamp1->setStatus('Yes');
        $manager->persist($foodstamp1);

        $foodstamp2 = new FsStatus();
        $foodstamp2->setStatus('Appl');
        $manager->persist($foodstamp2);

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('Unknown');
        $this->setReference('unk', $foodstamp);
        $manager->persist($foodstamp);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
