<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Tests\UserManagementTest.php

namespace Truckee\ProjectmanaBundle\Tests;

/**
 * UserManagementTest.
 */
class UserManagementTest extends TruckeeWebTestCase
{
    //optionsAdmin/?action=new&entity=User
    public function setup()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                ])->getReferenceRepository();
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

    public function testAddUser()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/optionsAdmin/?action=new&entity=User');
        $form = $crawler->filter('form')->form();
        $form['user[username]'] = 'bizarro';
        $form['user[fname]'] = 'Benny';
        $form['user[sname]'] = 'Borko';
        $form['user[email]'] = 'blipdot@bogus.info';
        $form['user[plainPassword]'] = 'password';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last login")')->count());
    }
}
