<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of InvitationTest
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class UserInvitationTest extends WebTestCase {

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

    public function testNewUser() {
        $client = static::createClient();
        $client->followRedirects();
        // submit invitation
        $crawler = $client->request('GET', '/register/invite/abcdefg');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Create new user")')->count());

        // create a password
        $crawler = $client->submitForm('Create!', [
            'new_user[plainPassword][first]' => '123Abc',
            'new_user[plainPassword][second]' => '123Abc',
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("You are now registered")')->count());

        // assure that invitation is removed
        $crawler = $client->request('GET', '/register/invite/abcdefg');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invalid registration data")')->count());
    }

    public function testAdminSendsInvitation() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $crawler = $client->submitForm('Sign in', [
            'username' => 'admin@bogus.info',
            'password' => 'manapw',
        ]);

        $crawler = $client->request('GET', '/admin/invite');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Create a new app user")')->count());
        $client->enableProfiler();

        $client->followRedirects(false);
        $crawler = $client->submitForm('Submit', [
            'invitation[email]' => 'bborko@bogus.info',
            'invitation[fname]' => 'Benny',
            'invitation[sname]' => 'Borko',
            'invitation[username]' => 'bborko',
        ]);
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());
    }

    public function testCurrentResetRequest() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/reset/hijkl');
        $client->followRedirects();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Set new password")')->count());
        $crawler = $client->submitForm('Create!', [
            'new_user[plainPassword][first]' => 'mynameis',
            'new_user[plainPassword][second]' => 'mynameis',
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Your password has been update")')->count());
    }

    public function testForgotPassword() {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');
        $link = $crawler->filter('a:contains("Forgot password?")')->link();
        $crawler = $client->click($link);

        // non-user
        $crawler = $client->submitForm('Submit', [
            'user_email[email]' => 'fiddle@deedee.org'
        ]);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email sent to address provided")')->count());

        //user
        $crawler = $client->request('GET', '/register/forgot');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Reset password form")')->count());
        $crawler = $client->submitForm('Submit', [
            'user_email[email]' => 'admin@bogus.info'
        ]);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email sent to address provided")')->count());
    }

    public function testLoggedInResetPassword() {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');
        $crawler = $client->submitForm('Sign in', [
            'username' => 'dberry@domain.com',
            'password' => 'password',
        ]);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Home")')->count());

        $crawler = $client->request('GET', '/register/reset');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Set new password")')->count());
        $crawler = $client->submitForm('Create!', [
            'new_user[plainPassword][first]' => 'mynameis',
            'new_user[plainPassword][second]' => 'mynameis',
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Your password has been update")')->count());
    }

    public function testExpiredForgottenRequest() {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register/reset/abcdefg');
        file_put_contents("G:\\Documents\\response.html", $client->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password forgotten link has expired")')->count());
    }

    public function tearDown() {
        unset($client);
    }

}
