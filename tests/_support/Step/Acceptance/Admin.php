<?php

namespace Step\Acceptance;

class Admin extends \AcceptanceTester
{

    public function loginAsAdmin()
    {
        $I = $this;
        $I->amOnPage('/login');
        $I->fillField('_username', 'admin');
        $I->fillField('_password', 'pmana314');
        $I->click('Login');
    }

}
