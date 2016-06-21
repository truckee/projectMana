<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//src\Mana\ClientBundle\DataFixtures\Test\StatisticsControllerTest.php
namespace Mana\ClientBundle\Tests;

/**
 * Description of StatisticsControllerTest.
 *
 * @author George
 */
class StatisticsControllerTest extends ManaWebTestCase
{
    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\Users',
                    'Mana\ClientBundle\DataFixtures\Test\Constants',
                    'Mana\ClientBundle\DataFixtures\Test\Households',
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
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for '.$reportDate.'")')->count());
    }

    public function testCenterGeneralStatistics()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/general');
        $form = $crawler->selectButton('Submit')->form();
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $form['report_criteria[center]']->select($truckee);
        $crawler = $this->client->submit($form);
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Statistics for '.$reportDate.'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Truckee")')->count());
    }

    public function testDetailsStatistics()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/details');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Distribution Details for '.$reportDate.'")')->count());
    }

    public function testMultipleContacts()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/reports/multi');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
        $date = new \DateTime('last month');
        $reportDate = date_format($date, 'F, Y');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("No instances of multiple same-date contacts found")')->count());
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
