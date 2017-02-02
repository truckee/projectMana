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

use Truckee\ProjectmanaBundle\Tests\TruckeeWebTestCase;

/**
 * DisabledOptionsTest
 *
 */
class DisabledOptionsTest extends TruckeeWebTestCase
{

    public function setup()
    {
        $this->client = static::createClient();
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
        $form = $crawler->selectButton('Log in')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'manapw';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testHouse3OptionsSelected()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $centerText = trim($crawler->filter("#household_center option:selected")->text());
        $this->assertEquals('Kings Beach', $centerText);
        $notText = trim($crawler->filter("#household_notfoodstamp option:selected")->text());
        $this->assertEquals('Not applied', $notText);
        $housingText = trim($crawler->filter("#household_housing option:selected")->text());
        $this->assertEquals('Owner', $housingText);
        $incomeText = trim($crawler->filter("#household_income option:selected")->text());
        $this->assertEquals('0 - 0', $incomeText);
        $this->assertEquals(0, $crawler->filter('html:contains("disabled")')->count());
    }

    public function testDisabledFsStatusHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_foodstamp');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
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
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Owner', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Owner")')->count());
    }

    public function testDisabledIncomeHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_income');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('0 - 0', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("0 - 0")')->count());
    }

    public function testDisabledNotfoodstampHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_notfoodstamp');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Not applied', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Not applied")')->count());
    }

    public function testDisabledFirstSiteHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_center');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Kings Beach', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Kings Beach")')->count());
    }

    public function testDisabledReasonHouseholdEdit()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house3')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $idCost = $this->fixtures->getReference('cost')->getId();
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $field = $crawler->filter('#household_reasons_' . $idCost);
        $checked = $field->attr('checked');

        $this->assertEquals('checked', $checked);
    }
    
    public function testDisabledOptionsNotAvailable()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house2')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertEquals(0, $crawler->filter('html:contains("Kings Beach")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Not applied")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("0 - 0")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Owner")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Unknown")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Housing/Utility cost")')->count());       

        $crawler = $this->client->request('GET', '/contact/' . $id . '/new');
        $this->assertEquals(0, $crawler->filter('html:contains("Kings Beach")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("General Dist")')->count());
    }

    public function testContactDisabledDescOption()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('contact1')->getId();
        $crawler = $this->client->request('GET', '/contact/'.$id.'/edit');

        $field = $crawler->filter('#contact_contactDesc');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('General Dist.', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("General Dist")')->count());
    }

    public function testContactDisabledSiteOption()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('contact1')->getId();
        $crawler = $this->client->request('GET', '/contact/'.$id.'/edit');

        $field = $crawler->filter('#contact_center');
        $disabled = $field->attr('disabled');
        $text = trim($field->filter('option[selected]')->text());
        $this->assertEquals('disabled', $disabled);
        $this->assertEquals('Tahoe City', $text);

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Tahoe City")')->count());
    }
}
