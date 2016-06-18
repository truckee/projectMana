<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mana\ClientBundle\Tests;

use Mana\ClientBundle\Tests\ManaWebTestCase;

/**
 * Description of StatusTest
 *
 * @author George
 */
class StatusTest extends ManaWebTestCase
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
    
    public function testStatusChange() {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/status');
        $form = $crawler->selectButton('Submit')->form();
        $year = date_format(new \DateTime(), 'Y');
        $active = $crawler->filter('#active' . $year);
        $form['status[' . $year . ']']->select('inactive');
        $crawler = $this->client->submit($form);
        $inactive = $crawler->filter('#inactive' . $year);
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household status updated")')->count());
        $this->assertEquals($active, $inactive);
    }
}
