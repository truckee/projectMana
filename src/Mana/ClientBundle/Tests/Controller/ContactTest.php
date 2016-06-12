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
                    'Mana\ClientBundle\DataFixtures\Test\AdminUser',
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

    public function testContact()
    {
        $crawler = $this->login();
        $id = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/' . $id . '/show');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household View")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("General")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Placer")')->count());
    }
}
