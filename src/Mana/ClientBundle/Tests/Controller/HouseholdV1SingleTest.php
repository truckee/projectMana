<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Tests\Controller\HouseholdV1SingleTest.php

namespace Mana\ClientBundle\Tests\Controller;

use Mana\ClientBundle\Tests\Controller\ManaWebTestCase;

/**
 * Description of HouseholdV1Test
 *
 * @author George
 */
class HouseholdV1SingleTest extends ManaWebTestCase
{

    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\AdminUser',
                    'Mana\ClientBundle\DataFixtures\Test\HouseholdV1Single'
                ])->getReferenceRepository();
    }

    public function login()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'pmana314';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testHouseholdV1SingleEdit()
    {
        $id = $this->fixtures->getReference('house')->getId();
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/' . $id . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("missing data MUST be corrected")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("DOB must be valid")')->count());
    }

}
