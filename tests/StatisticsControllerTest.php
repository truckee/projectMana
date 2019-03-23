<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests\StatisticsControllerTest.php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of StatisticsControllerTest.
 *
 * @author George Brooks
 */
class StatisticsControllerTest extends WebTestCase
{
    private $reference;

    public function setup()
    {
        $date = new \DateTime();
        $this->lastMonth = date_format(new \DateTime('last month'), 'F, Y');
        $this->client = static::createClient();
        $this->client->followRedirects();
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;
        $em = self::$container->get('doctrine')->getManager('test');
        $tables = [
            'AddressType', 'Assistance', 'Center', 'Contactdesc', 'County', 'Ethnicity',
            'Housing', 'Income', 'Notfoodstamp', 'Organization', 'Reason', 'Relationship',
            'State', 'Work', 'Contact', 'Address', 'Household', 'Member', 'User'
        ];
        $this->reference = [];
        foreach ($tables as $value) {
            $i = 1;
            $entities = $em->getRepository("App:" . $value)->findAll();
            foreach ($entities as $entity) {
                $this->reference[$value . $i] = $entity;
                $i ++;
            }
        }
    }

    /**
     * Login & go to Reports page
     *
     * @return DOM crawler
     */
    public function login()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
        $crawler = $this->client->submit($form);
        $link = $crawler->selectLink('Reports')->link();
        $crawler = $this->client->click($link);

        return $crawler;
    }

    public function testGeneralStatistics()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('General Statistics')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for ")')->count());
    }

    public function testDateValidator()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('General Statistics')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[startMonth]'] = 1;
        $form['report_criteria[endMonth]'] = 1;
        $form['report_criteria[startYear]'] = 2016;
        $form['report_criteria[endYear]'] = 2015;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0,
            $crawler->filter('html:contains("End date must be same or later than start date")')->count());
    }

    public function testCenterGeneralStatistics()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('General Statistics')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $truckee = $this->reference['Center3']->getId();
        $form['report_criteria[center]']->select($truckee);
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for ' . $this->lastMonth . '")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Truckee")')->count());
    }

    public function testCountyGeneralStatistics()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('General Statistics')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $placer = $this->reference['County1']->getId();
        $form['report_criteria[county]']->select($placer);
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for ' . $this->lastMonth . '")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Placer")')->count());
    }

    public function testDetailsStatistics()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Distribution details')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0,
            $crawler->filter('html:contains("Distribution Details for ' . $this->lastMonth . '")')->count());
    }

    public function testMultipleContacts()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Multiple contacts')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0,
            $crawler->filter('html:contains("Some Person")')->count());
    }

    public function testFoodbank()
    {
        $date = date_format(new \DateTime('last month'), 'Y/m');
        $crawler = $this->client->request('GET', '/reports/foodbank/' . $date);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Female Children")')->count());
    }

    public function testAssistanceSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Seeking services')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Seeking services")')->count());
    }

    public function testOrganizationSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Receiving services')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Receiving services")')->count());
    }

    public function testEmploymentSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Employment')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Employment profile")')->count());
    }

    public function testEmploymentCountyProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Employment')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[columnType]']->select('county');
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Employment profile")')->count());
    }

    public function testHousingSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Housing')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Housing profile")')->count());
    }

    public function testHousingCountyProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Housing')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[columnType]']->select('county');
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Housing profile")')->count());
    }

    public function testIncomeSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Income')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Income bracket")')->count());
    }

    public function testIncomeCountyProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Income')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[columnType]']->select('county');
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Income bracket")')->count());
    }

    public function testReasonSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Insufficient food')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("households not having enough food")')->count());
    }

    public function testReasonCountyProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Insufficient food')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[columnType]']->select('county');
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("households not having enough food")')->count());
    }

    public function testSNAPSiteProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('SNAP')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Receiving benefits")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Reason why not")')->count());
    }

    public function testSNAPCountyProfile()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('SNAP')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $form['report_criteria[columnType]']->select('county');
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Receiving benefits")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Reason why not")')->count());
    }

    public function testOtherServices()
    {
        $crawler = $this->login();
        $link = $crawler->selectLink('Other services')->link();
        $crawler = $this->client->click($link);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("sought")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Other Services Summary")')->count());
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->reference);
    }
}
