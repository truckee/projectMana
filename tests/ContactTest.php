<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests\ContactTest.php

namespace Tests;

//use App\Tests\TruckeeWebTestCase;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;;
//use Doctrine\DBAL\Driver\Mysqli;

/**
 * ContactTest.
 */
class ContactTest extends WebTestCase {

    private $reference;

    public function setup() {
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

    public function login() {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testHouse1ContactExists() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("General")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Placer")')->count());
    }

    public function testHouse3ContactExists() {
        $crawler = $this->login();
        $id = $this->reference['Household3']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("General")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Placer")')->count());
    }

    public function testNewContact() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/new');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $general = $this->reference['Contactdesc1']->getId();
        $truckee = $this->reference['Center3']->getId();
        $form['contact[contactdesc]'] = $general;
        $form['contact[center]'] = $truckee;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact added for household")')->count());
    }

    public function testNewContactValidation() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/new');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $general = $this->reference['Contactdesc1']->getId();
        $form['contact[contactdesc]'] = $general;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Site must be selected")')->count());
    }

    public function testEditContact() {
        $crawler = $this->login();
        $id = $id = $this->reference['Contact1']->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $face = $id = $this->reference['Contactdesc1']->getId();
        $form['contact[contactdesc]'] = $face;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact has been updated")')->count());
    }

    public function testDeleteContact() {
        $crawler = $this->login();
        $id = $id = $this->reference['Contact1']->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/delete');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Delete Contact")')->count());

        $form = $crawler->selectButton('Delete contact')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact has been deleted")')->count());

        $crawler = $this->client->request('GET', '/contact/4096/delete');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact does not exist")')->count());
    }

    public function testMostRecentContacts() {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/contact/latestReport/Most recent');
        $truckee = $this->reference['Center2']->getId();
        $form = $crawler->selectButton('Submit')->form();
        $form['select_center[center]'] = $truckee;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("No contacts found")')->count());
    }

    public function testFYToDateContacts() {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/contact/latestReport/FY to date');
        $incline = $this->reference['Center2']->getId();
        $form = $crawler->selectButton('Submit')->form();
        $form['select_center[center]'] = $incline;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("No contacts found")')->count());
    }

    public function testMostRecentResponse() {
        $crawler = $this->login();
        $truckee = $this->reference['Center3']->getId();
        $crawler = $this->client->request('GET', '/contact/latest/' . $truckee . '/Most recent');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Most recent contacts for Truckee")')->count());
    }

    public function testFYToDateResponse() {
        $crawler = $this->login();
        $truckee = $this->reference['Center3']->getId();
        $crawler = $this->client->request('GET', '/contact/latest/' . $truckee . '/FY to date');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("FY to date contacts for Truckee")')->count());
    }

    public function testHouseholdToContactsAndBack() {
        $crawler = $this->login();
        $id = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');
        $link = $crawler->selectLink('Household contacts')->link();
        $crawler = $this->client->click($link);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Contact for Household")')->count());

        $link = $crawler->selectLink('Return to household')->link();
        $crawler = $this->client->click($link);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
    }

    public function tearDown() {
        unset($this->client);
        unset($this->reference);
    }

}
