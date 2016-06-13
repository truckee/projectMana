<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


//src\Mana\ClientBundle\Tests\Controller\ContactTest.php

namespace Mana\ClientBundle\Tests\Controller;

use Mana\ClientBundle\Tests\Controller\ManaWebTestCase;

/**
 * ContactTest
 *
 */
class ContactTest extends ManaWebTestCase
{

    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                'Mana\ClientBundle\DataFixtures\Test\Users',
                'Mana\ClientBundle\DataFixtures\Test\Constants',
                'Mana\ClientBundle\DataFixtures\Test\Households',
                'Mana\ClientBundle\DataFixtures\Test\Contacts',
            ])->getReferenceRepository();
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

    public function testContactExists()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("General")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Placer")')->count());
    }

    public function testNewContact()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/new');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $general = $this->fixtures->getReference('general')->getId();
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $form['contact[contactDesc]'] = $general;
        $form['contact[center]'] = $truckee;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact added for household")')->count());
    }

    public function testNewContactValidation()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/new');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $general = $this->fixtures->getReference('general')->getId();
        $form['contact[contactDesc]'] = $general;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Site must be selected")')->count());
    }

    public function testEditContact()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('contact')->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Contact")')->count());

        $form = $crawler->selectButton('Submit contact')->form();
        $face = $this->fixtures->getReference('face')->getId();
        $form['contact[contactDesc]'] = $face;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact has been updated")')->count());
    }

    public function testDeleteContact()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('contact')->getId();
        $crawler = $this->client->request('GET', '/contact/' . $id . '/delete');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Delete Contact")')->count());

        $form = $crawler->selectButton('Delete contact')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact has been deleted")')->count());
    }

    public function testLatestContacts()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/contact/latestReport');
        $truckee = $this->fixtures->getReference('truckee')->getId();
        $form = $crawler->selectButton('Submit')->form();
        $form['select_center[center]'] = $truckee;
        $crawler = $this->client->submit($form);
file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No contacts found")')->count());

//        $crawler = $this->client->request('GET', '/contact/latestReport');
//        $tahoe = $this->fixtures->getReference('tahoe')->getId();
//        $form = $crawler->selectButton('Submit')->form();
//        $form['select_center[center]'] = $tahoe;
//        $crawler = $this->client->submit($form);
//        file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
//
//        $this->assertGreaterThan(0, $crawler->filter('html:contains("Open with")')->count());
    }
}
