<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Tests\MemberControllerTest.php

use Truckee\ProjectmanaBundle\Tests\TruckeeWebTestCase;

/**
 * MemberControllerTest.
 */
class MemberControllerTest extends TruckeeWebTestCase
{
    public function setup()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->fixtures = $this->loadFixtures([
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Users',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Constants',
                    'Truckee\ProjectmanaBundle\DataFixtures\Test\Households',
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

    public function testAddMemberValidation()
    {
        $crawler = $this->login();
        $houseId = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/member/add/'.$houseId);
        $form = $crawler->filter('form')->form();
        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("First name may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last name may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("DOB must be valid date or age")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Gender may not be blank")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ethnicity may not be blank")')->count());
    }

    public function testAddMember()
    {
        $crawler = $this->login();
        $houseId = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/member/add/'.$houseId);
        $form = $crawler->filter('form')->form();
        $form['member[fname]'] = 'Milli';
        $form['member[sname]'] = 'Vanilli';
        $form['member[dob]'] = '12';
        $eth = $this->fixtures->getReference('cau')->getId();
        $form['member[ethnicity]'] = $eth;
        $form['member[sex]'] = 'Female';
        $crawler = $this->client->submit($form);
        $crawler = $this->client->request('GET', '/household/'.$houseId.'/show');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Vanilli")')->count());
    }

    public function testMemberValidation()
    {
        $crawler = $this->login();
        $memberId = $this->fixtures->getReference('member')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$memberId);
        $form = $crawler->filter('form')->form();
        $values = $form->getValues();
        $fname = $values['member[fname]'];
        $crawler = $this->client->submit($form);

        $this->assertEquals('MoreThanOne', $fname);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("DOB must be valid date or age")')->count());
    }

    public function testChangeHead()
    {
        $crawler = $this->login();
        $member2Id = $this->fixtures->getReference('member2')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$member2Id);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Head?")')->count());

        $form = $crawler->filter('form')->form();
        $form['member[isHead]'] = 1;
        $crawler = $this->client->submit($form);

        $memberId = $this->fixtures->getReference('member')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$memberId);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Head?")')->count());
    }

    public function testExcludeMember()
    {
        $crawler = $this->login();
        $member2Id = $this->fixtures->getReference('member2')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$member2Id);
        file_put_contents("G:\\Documents\\response.html", $this->client->getResponse()->getContent());
        $form = $crawler->filter('form')->form();
        $form['member[include]'] = 0;
        $crawler = $this->client->submit($form);
        $houseId = $this->fixtures->getReference('house1')->getId();
        $crawler = $this->client->request('GET', '/household/'.$houseId.'/edit');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Excluded")')->count());
    }

    public function testMemberEdit()
    {
        $crawler = $this->login();
        $member2Id = $this->fixtures->getReference('member2')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$member2Id);
        $form = $crawler->filter('form')->form();
        $relation = $this->fixtures->getReference('related')->getId();
        $work = $this->fixtures->getReference('work')->getId();
        $form['member[relation]'] = $relation;
        $form['member[work]'] = $work;
        $crawler = $this->client->submit($form);

        $member2Id = $this->fixtures->getReference('member2')->getId();
        $crawler = $this->client->request('GET', '/member/edit/'.$member2Id);
        $form = $crawler->filter('form')->form();
        $values = $form->getValues();
        $formRelation = $values['member[relation]'];
        $formWork = $values['member[work]'];

        $this->assertEquals($relation, $formRelation);
        $this->assertEquals($work, $formWork);
    }
}
