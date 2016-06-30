<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


//src\Mana\ClientBundle\Tests\UserManagementTest.php

namespace Mana\ClientBundle\Tests;

use Mana\ClientBundle\Tests\ManaWebTestCase;

/**
 * UserManagementTest
 *
 */
class UserManagementTest extends ManaWebTestCase
{
    //optionsAdmin/?action=new&entity=User
    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\Users',
                    'Mana\ClientBundle\DataFixtures\Test\Constants',
                    'Mana\ClientBundle\DataFixtures\Test\Households',
                    'Mana\ClientBundle\DataFixtures\Test\HouseholdOptions',
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
