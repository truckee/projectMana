<?php

use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('see if this works!');
$I->loginAsAdmin();
$I->see('Welcome');
