<?php
//src\...\001-AddMemberCept.php

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
$I->see('Welcome');

$I->fillField('qtext', 'MoreThanOne Member');
$I->click('#search');
$I->see('Household View');

$I->see('Add member');

$I->click('Add member');
$I->waitForJS("return $.active == 0;", 2);
$I->see('Add household member');

$I->fillField('#member_fname', 'Hieronymous');
$I->fillField('#member_sname', 'Bosch');
$I->selectOption('form select[name="member[sex]"]', 'Male');
$I->selectOption('form select[name="member[ethnicity]"]', 'Cau');
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 2);
$I->see('DOB must be valid date or age');

$I->fillField("#member_dob", 44);
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 2);
$I->see('Hieronymous Bosch has been added');
