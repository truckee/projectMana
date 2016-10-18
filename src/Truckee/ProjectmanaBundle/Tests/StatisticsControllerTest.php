<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Tests\StatisticsControllerTest.php

namespace Truckee\ProjectmanaBundle\Tests;

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
        $firstDay = new \DateTime(date_format($date, 'F') . ' 1,' . date_format($date, 'Y'));
        $lastMonth = $firstDay->sub(new \DateInterval('P1D'));
        $this->lastMonth = date_format($lastMonth, 'F, Y');
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Contacts',
            ])->getReferenceRepository();
//        file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
    }

    public function login()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'manapw';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testGeneralStatistics()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/general');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for ' . $this->lastMonth . '")')->count());
    }

    public function testDateValidator()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/general');
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
        $crawler = $this->client->request('GET', '/reports/general');
        $form = $crawler->selectButton('Submit')->form();
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $form['report_criteria[center]']->select($truckee);
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for ' . $this->lastMonth . '")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Truckee")')->count());
    }

    public function testDetailsStatistics()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/details');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0,
            $crawler->filter('html:contains("Distribution Details for ' . $this->lastMonth . '")')->count());
    }

    public function testMultipleContacts()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/multi');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0,
            $crawler->filter('html:contains("No instances of multiple same-date contacts found")')->count());
    }

    public function testFoodbank()
    {
        $crawler = $this->client->request('GET', '/reports/foodbank/2016/6');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Female Children")')->count());
    }

    public function testEmploymentProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/employmentProfile');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Employment profile")')->count());
    }

    public function testHousingProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/housingProfile');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Housing profile")')->count());
    }

    public function testIncomeProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/incomeProfile');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Income bracket")')->count());
    }

    public function testReasonProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/reasonProfile');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("households not having enough food")')->count());
    }

    public function testSNAPProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/snapProfile');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Receiving benefits")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("How much")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Reason why not")')->count());
    }
}
