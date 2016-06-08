<?php

/*
 * This file is part of the Truckee\Match package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Tests\Controller\HouseholdControllerTest.php

namespace Mana\ClientBundle\Tests\Controller;

use Mana\ClientBundle\Tests\Controller\ManaWebTestCase;

/**
 * Description of HouseholdControllerTest
 *
 * @author George
 */
class HouseholdControllerTest extends ManaWebTestCase
{

    public function setup()
    {
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\AdminUser',
                    'Mana\ClientBundle\DataFixtures\Test\Constants',
                    'Mana\ClientBundle\DataFixtures\Test\HouseholdV1Single',
                    'Mana\ClientBundle\DataFixtures\Test\HouseholdV1Many',
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

    public function submitNewHousehold()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/new');
        $form = $crawler->selectButton('Submit')->form();
        $form['member[fname]'] = 'Benny';
        $form['member[sname]'] = 'Borko';
        $form['member[dob]'] = '44';
        $form['member[sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['member[ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household_required[center]'] = $tahoe;
        $form['household_required[complianceDate]'] = '2/1/2016';
        $form['household_required[sharedDate]'] = '2/1/2016';

        return $this->client->submit($form);
    }

    public function testLogin() {
        $crawler = $this->login();
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
    }
    
    public function testNewHousehold()
    {
        $crawler = $this->submitNewHousehold();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());
    }

    public function testFoodStamps()
    {
        $crawler = $this->submitNewHousehold();
        $crawler = $this->client->request('GET', '/home');
        $form = $crawler->filter('#household_search')->form();
        $form['qtext'] = 'Benny Borko';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Food stamps? Unknown")')->count());
    }

    public function testDuplicateName()
    {
        $crawler = $this->submitNewHousehold();
        $crawler = $this->client->request('GET', '/household/new');

        $form = $crawler->selectButton('Submit')->form();
        $form['member[fname]'] = 'MoreThanOne';
        $form['member[sname]'] = 'Membrane';
        $form['member[dob]'] = '44';
        $form['member[sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['member[ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household_required[center]'] = $tahoe;
        $form['household_required[complianceDate]'] = '2/1/2016';
        $form['household_required[sharedDate]'] = '2/1/2016';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add new head of house")')->count());
    }

}
