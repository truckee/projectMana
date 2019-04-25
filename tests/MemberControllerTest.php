<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//tests\MemberControllerTest.php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * MemberControllerTest.
 */
class MemberControllerTest extends WebTestCase
{
    private $reference;

    public function setup()
    {
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

    public function login()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['username'] = 'admin@bogus.info';
        $form['password'] = 'manapw';
        $crawler = $this->client->submit($form);

        return $crawler;
    }

    public function testAddMemberValidation()
    {
        $crawler = $this->login();
        $houseId = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/member/add/' . $houseId);
        $crawler = $this->client->submitForm('Submit', []);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("First name may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last name may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invalid date or age entry")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Gender may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ethnicity may not be blank")')->count());
    }

    public function testAddMember()
    {
        $crawler = $this->login();
        $houseId = $this->reference['Household1']->getId();
        $eth = $this->reference['Ethnicity3']->getId();
        $crawler = $this->client->request('GET', '/member/add/' . $houseId);
        $crawler = $this->client->submitForm('Submit', [
            'member[fname]' => 'Vanilli',
            'member[sname]' => 'Milli',
            'member[dob]' => '12',
            'member[ethnicity]' => $eth,
            'member[sex]' => 'Female',
        ]);
        $crawler = $this->client->request('GET', '/household/' . $houseId . '/show');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Vanilli")')->count());
    }

    public function testMemberValidation()
    {
        $crawler = $this->login();
        $memberId = $this->reference['Member1']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $memberId);

        $form = $crawler->selectButton('Submit')->form();
        $values = $form->getValues();
        $fname = $values['member[fname]'];
        $crawler = $this->client->submit($form);

        $this->assertEquals('MoreThanOne', $fname);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invalid date or age entry")')->count());
    }

    public function testChangeHead()
    {
        $crawler = $this->login();
        $member2Id = $this->reference['Member2']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $member2Id);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Head?")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $form['member[isHead]'] = 1;
        $crawler = $this->client->submit($form);

        $memberId = $this->reference['Member1']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $memberId);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Head?")')->count());
    }

    public function testExcludeMember()
    {
        $crawler = $this->login();
        $member2Id = $this->reference['Member2']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $member2Id);
        $form = $crawler->selectButton('Submit')->form();
        $form['member[include]'] = 0;
        $crawler = $this->client->submit($form);

        $houseId = $this->reference['Household1']->getId();
        $crawler = $this->client->request('GET', '/household/' . $houseId . '/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Excluded")')->count());
    }

    public function testMemberEdit()
    {
        $crawler = $this->login();
        $member2Id = $this->reference['Member2']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $member2Id);
        $form = $crawler->selectButton('Submit')->form();
        $relation = $this->reference['Relationship1']->getId();
        $work = $this->reference['Work1']->getId();
        $form['member[relation]'] = $relation;
        $form['member[jobs]'][0]->tick();
        $crawler = $this->client->submit($form);

        $member2Id = $this->reference['Member2']->getId();
        $crawler = $this->client->request('GET', '/member/edit/' . $member2Id);
        $form = $crawler->selectButton('Submit')->form();
        $values = $form->getValues();
        $formRelation = $values['member[relation]'];
        $formWork = $values["member[jobs][0]"];

        $this->assertEquals($relation, $formRelation);
        $this->assertEquals($work, $formWork);
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->reference);
    }
}
