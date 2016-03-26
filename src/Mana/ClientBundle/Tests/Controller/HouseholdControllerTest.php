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
        $this->loadFixtures(array(
            'Mana\ClientBundle\DataFixtures\Test\AdminUser',
        ));
        $this->client = static::makeClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Mana\ClientBundle\DataFixtures\Test\AdminUser'
                ])->getReferenceRepository();
//        file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
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

    public function submitNewHousehold()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/household/new');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("All items required")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $form['household[members][0][fname]'] = 'Benny';
        $form['household[members][0][sname]'] = 'Borko';
        $form['household[members][0][dob]'] = '44';
        $form['household[members][0][sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['household[members][0][ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household[center]'] = $tahoe;
        $form['household[complianceDate]'] = '2/1/2016';
        $form['household[sharedDate]'] = '2/1/2016';

        return $this->client->submit($form);
    }

    public function testNewHousehold()
    {
        $crawler = $this->submitNewHousehold();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household Edit")')->count());
    }

    public function testFoodStamps()
    {
        $crawler = $this->submitNewHousehold();
        $unk = $this->fixtures->getReference('unk')->getId();
        $test = $crawler->filter('div.title')->text();
        $start = strpos($test, ':');
        $id = substr($test, $start + 2, 5);
        $crawler = $this->client->request('GET', '/home');
        $form = $crawler->selectButton('Search')->form();
        $form['qtext'] = $id;
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Food stamps? Unknown")')->count());
    }

    public function testDuplicateName()
    {
        $crawler = $this->submitNewHousehold();
        $crawler = $this->client->request('GET', '/household/new');

        $form = $crawler->selectButton('Submit')->form();
        $form['household[members][0][fname]'] = 'Benny';
        $form['household[members][0][sname]'] = 'Broko';
        $form['household[members][0][dob]'] = '44';
        $form['household[members][0][sex]'] = 'Male';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['household[members][0][ethnicity]'] = $eth;
        $tahoe = $this->fixtures->getReference('tahoe')->getId();
        $form['household[center]'] = $tahoe;
        $form['household[complianceDate]'] = '2/1/2016';
        $form['household[sharedDate]'] = '2/1/2016';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Borko")')->count());
    }

}
