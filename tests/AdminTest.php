<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests\AdminTest.php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * AdminControllerTest.
 */
class AdminTest extends WebTestCase {

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
//        $this->fixtures = $this->loadFixtures([
//                    'App\DataFixtures\Test\Users',
//                ])->getReferenceRepository();
    }

    public function testAdminLogin() {

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Welcome")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Admin")')->count());
    }

    public function testUserLogin() {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'dberry@bogus.info';
        $form['password'] = 'mana';
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Project MANA")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Admin")')->count());
    }

    public function tearDown() {
        unset($this->client);
        unset($this->reference);
    }

}
