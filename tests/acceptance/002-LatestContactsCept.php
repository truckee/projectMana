<?php
//src\...\002-LatestContactsCept.php

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
$I->wantTo('Test most recent contacts');
$I->loginAsAdmin();
$I->click('New contacts');
$I->see('Add contacts');

$I->selectOption('form select[name="contact[contactDesc]"]', 'FACE');
$I->selectOption('form select[name="contact[center]"]', 'Truckee');
$I->see('Collecting data');

$I->waitForJS("return $.active == 0;", 2);
$I->see('Most recent contacts for Truckee');
$I->click('Submit contacts');

$I->fillField("#contact_householdId", 1);
$I->click('button#contact_household_button');
$I->waitForJS("return $.active == 0;", 2);
$I->see('Head, Single');
$I->click('Submit contacts');

$I->fillField('qtext', 'Single Head');
$I->click('#search');
$I->see('FACE');
