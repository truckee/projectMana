<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Tests\DisabledOptionsTest.php

namespace Truckee\ProjectmanaBundle\Tests;

/**
 * DisabledOptionsTest
 *
 */
class DisabledOptionsTest extends TruckeeWebTestCase
{

    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Contacts',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                'Truckee\ProjectmanaBundle\DataFixtures\Test\DisabledConstants',
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

    public function testDisabledFirstSiteHouseholdNew()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/new');

        $this->assertEquals(0, $crawler->filter('html:contains("Kings Beach")')->count());
    }

    public function testDisabledFirstSiteHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house2')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_center');
        $disabled = $field->attr('disabled');
        $text = $field->filter('option[selected]')->text();
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Kings Beach', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Kings Beach")')->count());
    }

    public function testDisabledFsStatusHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_foodstamp');
        $disabled = $field->attr('disabled');
        $text = $field->filter('option[selected]')->text();
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Unknown', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Unknown")')->count());
    }

    public function testDisabledHousingHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_housing');
        $disabled = $field->attr('disabled');
        $text = $field->filter('option[selected]')->text();
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Owner', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Owner")')->count());
    }

    public function testDisabledIncomeHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_income');
        $disabled = $field->attr('disabled');
        $text = $field->filter('option[selected]')->text();
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('0 - 5', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("0 - 5<")')->count());
    }

    public function testDisabledNotfoodstampHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_notfoodstamp');
        $disabled = $field->attr('disabled');
        $text = $field->filter('option[selected]')->text();
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Not applied', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Not applied")')->count());
    }

    public function testDisabledOptionsMissing()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house2')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertEquals(0, $crawler->filter('html:contains("Kings Beach")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Unknown")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Owner")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("0 - 5<")')->count());
    }
}
