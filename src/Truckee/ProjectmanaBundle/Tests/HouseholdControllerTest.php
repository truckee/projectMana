<?php

/*
 * This file is part of the Truckee\Match package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Tests\HouseholdControllerTest.php

namespace Truckee\ProjectmanaBundle\Tests;

/**
 * Description of HouseholdControllerTest.
 *
 * @author George Brooks
 */
class HouseholdControllerTest extends TruckeeWebTestCase
{
    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\HouseholdOptions',
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

    public function submitNewHousehold()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/new');
        $form = $crawler->selectButton('Submit')->form();
        $form['member[fname]'] = 'Benny';
        $form['member[sname]'] = 'Borko';
        $form['member[dob]'] = '44';
        $form['member[sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['member[ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household_required[center]'] = $tahoe;
        $form['household_required[complianceDate]'] = '2/1/2016';
        $form['household_required[sharedDate]'] = '2/1/2016';

        return $this->client->submit($form);
    }

    public function testLogin()
    {
        $crawler = $this->login();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
    }

    public function testNewHousehold()
    {
        $crawler = $this->submitNewHousehold();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());
    }

    public function testFoodStamps()
    {
        $crawler = $this->submitNewHousehold();
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->filter('#household_search')->form();
        $form['qtext'] = 'Benny Borko';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Food stamps? Unknown")')->count());
    }

    public function testDuplicateName()
    {
        $crawler = $this->submitNewHousehold();
        $crawler = $this->client->request('GET', '/household/new');

        $form = $crawler->selectButton('Submit')->form();
        $form['member[fname]'] = 'MoreThanOne';
        $form['member[sname]'] = 'Membrane';
        $form['member[dob]'] = '44';
        $form['member[sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['member[ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household_required[center]'] = $tahoe;
        $form['household_required[complianceDate]'] = '2/1/2016';
        $form['household_required[sharedDate]'] = '2/1/2016';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add new head of house")')->count());
    }

    public function testShowHousehold()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/'.$id.'/show');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
    }

    public function testEditHousehold()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/'.$id.'/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $form['household[compliance]'] = 1;
        $form['household[shared]'] = 1;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Compliance date required")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Shared date required")')->count());

        $truckee = $this->fixtures->getReference('truckee')->getId();
        $form['household[complianceDate]'] = '6/1/2015';
        $form['household[sharedDate]'] = '6/1/2015';
        $form['household[center]'] = $truckee;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household updated")')->count());
    }

    public function testNoSearchCriteria()
    {
        $crawler = $this->login();
        $form = $crawler->filter('#household_search')->form();
        $form['qtext'] = '';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("No search criteria were entered")')->count());
    }

    public function testHouseholdNotFound()
    {
        $crawler = $this->login();
        $form = $crawler->filter('#household_search')->form();
        $form['qtext'] = '999';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sorry, household not found")')->count());
    }

    public function testNoHouseholdsFound()
    {
        $crawler = $this->login();
        $form = $crawler->filter('#household_search')->form();
        $form['qtext'] = 'Alien Creatures';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sorry, no households were found")')->count());
    }

    public function testValidateHousehold()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/'.$id.'/edit');

        $foodstamp = $this->fixtures->getReference('fsNo')->getId();
        $income = $this->fixtures->getReference('medIncome')->getId();
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $future = date_format(new \DateTime('next year'), 'm/d/Y');

        $form = $crawler->selectButton('Submit')->form();
        $form['household[foodstamp]'] = $foodstamp;
        $form['household[income]'] = $income;
        $form['household[center]'] = $truckee;
        $form['household[compliance]'] = '1';
        $form['household[complianceDate]'] = $future;
        $form['household[shared]'] = '1';
        $form['household[sharedDate]'] = $future;
        $form['household[phones][0][phoneNumber]'] = '12367';
        $form['household[phones][0][areacode]'] = '12';

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Phone # must be xxx-yyyy")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Area code must be 3 digits")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Date may not be in future")')->count());
    }

    public function testValidateSharedDate()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/'.$id.'/edit');

        $foodstamp = $this->fixtures->getReference('fsNo')->getId();
        $income = $this->fixtures->getReference('medIncome')->getId();
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $future = date_format(new \DateTime('next year'), 'm/d/Y');

        $form = $crawler->selectButton('Submit')->form();
        $form['household[foodstamp]'] = $foodstamp;
        $form['household[income]'] = $income;
        $form['household[center]'] = $truckee;
        $form['household[shared]'] = '1';
        $form['household[sharedDate]'] = $future;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Date may not be in future")')->count());
    }
}