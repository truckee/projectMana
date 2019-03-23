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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of StatusTest.
 *
 * @author George Brooks
 */
class StatusTest extends WebTestCase {

    private $reference;

    public function setup() {
        $this->client = static::createClient();
        $this->client->followRedirects();

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;
        $em = self::$container->get('doctrine')->getManager('test');
        $tables = [
            'AddressType', 'Assistance', 'Center', 'Contactdesc', 'County', 'Ethnicity',
            'Housing', 'Income', 'Notfoodstamp', 'Organization', 'Reason', 'Relationship',
            'State', 'Work', 'Contact', 'Address', 'Household', 'Member', 'User'
        ];
        $this->reference = [];
        foreach ($tables as $value) {
            $i = 1;
            $entities = $em->getRepository("App:" . $value)->findAll();
            foreach ($entities as $entity) {
                $this->reference[$value . $i] = $entity;
                $i ++;
            }
        }
    }

    public function login() {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testStatusChange() {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', '/status');
file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
        $form = $crawler->selectButton('Submit')->form();
        $year = date_format(new \DateTime(), 'Y');
        $activeBefore = trim($crawler->filter('#active' . $year)->text());
        $form['status[' . $year . ']']->select('inactive');
        $crawler = $this->client->submit($form);
        $inactive = trim($crawler->filter('#inactive' . $year)->text());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Household status updated")')->count());
        $this->assertEquals($activeBefore, $inactive);

        $form = $crawler->selectButton('Submit')->form();
        $form['status[' . $year . ']']->select('active');
        $crawler = $this->client->submit($form);
        $activeAfter = trim($crawler->filter('#active' . $year)->text());

        $this->assertEquals($activeBefore, $activeAfter);
    }

    public function tearDown() {
        unset($this->client);
        unset($this->reference);
    }

}
