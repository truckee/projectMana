<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Truckee\ProjectmanaBundle\Tests\TruckeeWebTestCase;

/**
 * Description of StatusTest.
 *
 * @author George Brooks
 */
class StatusTest extends TruckeeWebTestCase
{
    public function setup()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Contacts',
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

    public function testStatusChange()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/status');
        $form = $crawler->selectButton('Submit')->form();
        $year = date_format(new \DateTime(), 'Y');
        $activeBefore = trim($crawler->filter('#active'.$year)->text());
        $form['status['.$year.']']->select('inactive');
        $crawler = $this->client->submit($form);
        $inactive = trim($crawler->filter('#inactive'.$year)->text());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household status updated")')->count());
        $this->assertEquals($activeBefore, $inactive);

        $form = $crawler->selectButton('Submit')->form();
        $form['status['.$year.']']->select('active');
        $crawler = $this->client->submit($form);
        $activeAfter = trim($crawler->filter('#active'.$year)->text());

        $this->assertEquals($activeBefore, $activeAfter);
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->fixtures);
    }
}
