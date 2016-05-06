<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Tests\Controller\HouseholdV1ManyTest.php

namespace Mana\ClientBundle\Tests\Controller;

use Mana\ClientBundle\Tests\Controller\ManaWebTestCase;

/**
 * Description of HouseholdV1Test
 *
 * @author George
 */
class HouseholdV1ManyTest extends ManaWebTestCase
{

    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\AdminUser',
                    'Mana\ClientBundle\DataFixtures\Test\Constants',
                    'Mana\ClientBundle\DataFixtures\Test\HouseholdV1Many'
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

    public function testHouseholdV1ManyEdit()
    {
        $id = $this->fixtures->getReference('house')->getId();
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("new head of household from included")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("new head of household from included")')->count());
    }

}
