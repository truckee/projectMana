<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


//src\Mana\ClientBundle\DataFixtures\Test\AdminControllerTest.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Mana\ClientBundle\Tests\Controller\ManaWebTestCase;

/**
 * AdminControllerTest
 *
 */
class AdminControllerTest extends ManaWebTestCase
{
    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\Users',
                ])->getReferenceRepository();
    }

    public function testAdminLogin()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'manapw';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
    }

    public function testUserLogin()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'dberry';
        $form['_password'] = 'mana';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
    }

 
    public function testAccessError()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'dberry';
        $form['_password'] = 'mana';
        $crawler = $this->client->submit($form);
        $crawler = $this->client->request('GET', '/admin');
        $code = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(403, $code);
    }
}
