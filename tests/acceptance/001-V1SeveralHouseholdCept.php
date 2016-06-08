<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('Test Add member');
$I->loginAsAdmin();
$I->fillField('qtext', 'MoreThanOne Member');
$I->click('#search');
$I->see('Select');
$I->click('Select');
$I->see('Household View');
$I->see('Add member');
$I->click('#addMember');
$I->waitForJS("return $.active == 0;", 20);
//$I->checkOption("#isHead2");
$I->see('Add household member');
