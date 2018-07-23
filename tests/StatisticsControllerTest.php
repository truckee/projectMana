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

use Truckee\ProjectmanaBundle\Tests\TruckeeWebTestCase;

/**
 * Description of StatisticsControllerTest.
 *
 * @author George Brooks
 */
class StatisticsControllerTest extends TruckeeWebTestCase
{

    public function setup()
    {
        $date = new \DateTime();
        $this->lastMonth = date_format(new \DateTime('last month'), 'F, Y');
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Contacts',
            ])->getReferenceRepository();
    }

    /**
     * Login & go to Reports page
     *
     * @return DOM crawler
     */
    public function login()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Log in')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'manapw';
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
        $truckee = $this->fixtures->getReference('truckee')->getId();
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
        $placer = $this->fixtures->getReference('placer')->getId();
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

    public function tearDown()
    {
        unset($this->client);
        unset($this->fixtures);
    }
}
