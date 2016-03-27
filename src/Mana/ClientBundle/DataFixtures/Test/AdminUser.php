<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\AdminUser.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mana\ClientBundle\Entity\User;
use Mana\ClientBundle\Entity\Ethnicity;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Entity\County;
use Mana\ClientBundle\Entity\FsStatus;

/**
 * Description of AdminUser
 *
 * @author George
 */
class AdminUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($userAdmin);
        $password = $encoder->encodePassword('pmana314', $userAdmin->getSalt());
        $userAdmin->setPassword($password);
        $userAdmin->setRole('ROLE_ADMIN');
        $userAdmin->setFname('Benny');
        $userAdmin->setSname('Borko');
        $userAdmin->setEmail('bborko@bogus.info');
        $userAdmin->setIsActive(TRUE);

        $manager->persist($userAdmin);
        $manager->flush();
        $manager->clear();

        $eth = new Ethnicity();
        $eth->setEthnicity('Caucasian');
        $this->setReference('cau', $eth);
        $eth->setAbbreviation('Cau');
        $eth->setEnabled(TRUE);
        $manager->persist($eth);
        $manager->flush();

//        $eth = new Ethnicity();
//        $eth->setEthnicity('African-American');
//        $eth->setAbbreviation('AfrAm');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Asian');
//        $eth->setAbbreviation('Asian');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Hispanic');
//        $eth->setAbbreviation('Hisp');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Native American');
//        $eth->setAbbreviation('NtvAm');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Hawaiian/Pacific Islander');
//        $eth->setAbbreviation('HaPI');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Other');
//        $eth->setAbbreviation('Other');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();
//
//        $eth = new Ethnicity();
//        $eth->setEthnicity('Unknown');
//        $eth->setAbbreviation('Unk');
//        $eth->setEnabled(TRUE);
//        $manager->persist($eth);
//        $manager->flush();

        $manager->clear();

        $county = new County();
        $county->setCounty('Placer');
        $county->setEnabled(1);
        $manager->persist($county);
        $manager->flush();

        $county = new County();
        $county->setCounty('Nevada');
        $county->setEnabled(1);
        $manager->persist($county);
        $manager->flush();

        $county = new County();
        $county->setCounty('Washoe');
        $county->setEnabled(1);
        $manager->persist($county);
        $manager->flush();

        $manager->clear();

        $center = new Center();
        $center->setCenter('Tahoe City');
        $placer = $manager->getRepository('ManaClientBundle:County')->findOneBy(array('county' => 'Placer'));
        $center->setCounty($placer);
        $center->setEnabled(1);
        $this->setReference('tahoe', $center);
        $manager->persist($center);
        $manager->flush();

        $center = new Center();
        $center->setCenter('Kings Beach');
        $center->setCounty($placer);
        $center->setEnabled(1);
        $manager->persist($center);
        $manager->flush();

        $center = new Center();
        $center->setCenter('Truckee');
        $nevada = $manager->getRepository('ManaClientBundle:County')->findOneBy(array('county' => 'Nevada'));
        $center->setCounty($nevada);
        $center->setEnabled(1);
        $manager->persist($center);
        $manager->flush();

        $center = new Center();
        $center->setCenter('Incline Village');
        $washoe = $manager->getRepository('ManaClientBundle:County')->findOneBy(array('county' => 'Washoe'));
        $center->setCounty($washoe);
        $center->setEnabled(1);
        $manager->persist($center);
        $manager->flush();

        $manager->clear();

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('No');

        $manager->persist($foodstamp);
        $manager->flush();

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('Yes');

        $manager->persist($foodstamp);
        $manager->flush();

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('Appl');

        $manager->persist($foodstamp);
        $manager->flush();

        $foodstamp = new FsStatus();
        $foodstamp->setStatus('Unknown');

        $this->setReference('unk', $foodstamp);
        $manager->persist($foodstamp);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}
