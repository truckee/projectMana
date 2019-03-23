<?php

/*
 * This file is part of the Truckee\Match package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//tests\HouseholdControllerTest.php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of HouseholdControllerTest.
 *
 * @author George Brooks
 */
class HouseholdControllerTest extends WebTestCase
{

    private $reference;
    
    public function setup()
    {
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
//        file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
    }

    public function login()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
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
        $eth = $this->reference['Ethnicity1']->getId();
        $form['member[ethnicity]'] = $eth;
        $tahoe = $this->reference['Center1']->getId();
        $form['household[center]'] = $tahoe;
        $form['household[arrivalmonth]'] = 5;
        $form['household[arrivalyear]'] = 2018;

        return $this->client->submit($form);
    }

    public function testNewHousehold() {
        $crawler = $this->submitNewHousehold();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());
    }

    public function testDuplicateName() {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/new');
        $eth = $this->reference['Ethnicity3']->getId();
        $tahoe = $this->reference['Center3']->getId();
        $crawler = $this->client->submitForm('submit',
                [
                    'member[fname]' => 'MoreThanOne',
                    'member[sname]' => 'Membrane',
                    'member[dob]' => '44',
                    'member[sex]' => 'Male',
                    'member[ethnicity]' => $eth,
                    'household[center]' => $tahoe,
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add new head of house")')->count());
    }

    public function testShowHousehold() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
    }

    public function testEditHousehold() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $form['household[compliance]'] = 1;
        $form['household[shared]'] = 1;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Compliance date required")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Shared date required")')->count());

        $truckee = $this->reference['Center3']->getId();
        $form['household[complianceDate]'] = '6/1/2015';
        $form['household[sharedDate]'] = '6/1/2015';
        $form['household[center]'] = $truckee;
        $form['household[arrivalmonth]'] = 5; //May
        $form['household[arrivalyear]'] = 2017;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household updated")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("May")')->count());
    }

    public function testNoSearchCriteria() {
        $crawler = $this->login();
        $crawler = $this->client->submitForm('_search', []);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("No search criteria were entered")')->count());
    }

    public function testHouseholdNotFound() {
        $crawler = $this->login();
        $form = $crawler->selectButton('_search')->form();
        $form['qtext'] = '999';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sorry, household not found")')->count());
    }

    public function testNoHouseholdsFound() {
        $crawler = $this->login();
        $form = $crawler->selectButton('_search')->form();
        $form['qtext'] = 'Alien Creatures';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sorry, no households were found")')->count());
    }

    public function testValidateHousehold() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $income = $this->reference['Income2']->getId();
        $truckee = $this->reference['Center3']->getId();
        $future = date_format(new \DateTime('next year'), 'm/d/Y');

        $form = $crawler->selectButton('Submit')->form();
        $form['household[income]'] = $income;
        $form['household[center]'] = $truckee;
        $form['household[compliance]'] = '1';
        $form['household[complianceDate]'] = $future;
        $form['household[shared]'] = '1';
        $form['household[sharedDate]'] = $future;
        $form['household[phoneNumber]'] = '12367';
        $form['household[areacode]'] = '12';

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Phone # must be xxx-yyyy")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Area code must be 3 digits")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Date may not be in future")')->count());
    }

    public function testValidateSharedDate() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $income = $this->reference['Income2']->getId();
        $truckee = $this->reference['Center3']->getId();
        $future = date_format(new \DateTime('next year'), 'm/d/Y');

        $form = $crawler->selectButton('Submit')->form();
        $form['household[income]'] = $income;
        $form['household[center]'] = $truckee;
        $form['household[shared]'] = '1';
        $form['household[sharedDate]'] = $future;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Date may not be in future")')->count());
    }

    public function testOneAddress() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertEquals(2, $crawler->filter('input[type=radio]')->count());
    }

    public function testNoAddress() {
        $crawler = $this->login();
        $id = $this->reference['Household2']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertEquals(4, $crawler->filter('input[type=radio]')->count());
    }

    public function testAddressSubmit() {
        $crawler = $this->login();
        $id = $this->reference['Household3']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $form = $crawler->selectButton('Submit')->form();
        $form["household[physicalAddress][physical]"]->select("1");
        $form["household[physicalAddress][address][line1]"] = '12 NewLine';
        $truckee = $this->reference['Center3']->getId();
        $form['household[center]'] = $truckee;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household updated")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("12 NewLine")')->count());
    }

    public function testHouse3NoOptionsDisabled() {
        $crawler = $this->login();
        $id = $this->reference['Household3']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $centerText = trim($crawler->filter("#household_center option:selected")->text());
        $this->assertEquals('Truckee', $centerText);
        $notText = trim($crawler->filter("#household_notfoodstamp option:selected")->text());
        $this->assertEquals('Not applied', $notText);
        $housingText = trim($crawler->filter("#household_housing option:selected")->text());
        $this->assertEquals('Owner', $housingText);
        $incomeText = trim($crawler->filter("#household_income option:selected")->text());
        $this->assertEquals('0 - 0', $incomeText);
//        $this->assertEquals(0, $crawler->filter('html:contains("disabled")')->count());
    }

    public function testServiceRequested() {
        $crawler = $this->login();
        $id = $this->reference['Household3']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $form = $crawler->selectButton('Submit')->form();
        $form['household[assistances]'][0]->tick();
        $form['household[seeking]'] = 'Demon chasing';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Other")')->count());
    }

    public function testServiceUsed() {
        $crawler = $this->login();
        $id = $this->reference['Household3']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');
        $form = $crawler->selectButton('Submit')->form();
        $form['household[organizations]'][0]->tick();
        $form['household[receiving]'] = 'Marmot fund';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Other")')->count());
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->reference);
    }
}
