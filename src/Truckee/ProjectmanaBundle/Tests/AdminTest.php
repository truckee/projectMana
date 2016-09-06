<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Tests\AdminControllerTest.php

namespace Truckee\ProjectmanaBundle\Tests;

/**
 * AdminControllerTest.
 */
class AdminTest extends TruckeeWebTestCase
{
    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
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
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Options & users")')->count());
    }

    public function testUserLogin()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'dberry';
        $form['_password'] = 'mana';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Options & users")')->count());
    }
}
