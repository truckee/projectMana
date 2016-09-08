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
$I->fillField('qtext', 'MoreThanOne Member');
$I->click('#search');
$I->click('Select');
$I->click('Add member');
$I->waitForJS("return $.active == 0;", 10);
$I->see('Add household member');

$I->fillField('#member_fname', 'Hieronymous');
$I->fillField('#member_sname', 'Bosch');
$I->selectOption('form select[name="member[sex]"]', 'Male');
$I->selectOption('form select[name="member[ethnicity]"]', 'Cau');
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 10);
$I->see('DOB must be valid date or age');

$I->fillField("#member_dob", 44);
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 10);
$I->see('Hieronymous Bosch has been added');

$I->click('Close');
$I->click('#memberId3');
$I->waitForJS("return $.active == 0;", 10);
$I->see('Edit household member');
$I->see('Include?');

$I->click('#member_isHead');
$I->waitForJS("return $.active == 0;", 1);
$I->dontSee('Include?');

$I->fillField("#member_dob", 12);
$I->selectOption('form select[name="member[sex]"]', 'Female');
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 10);
$I->see('has been updated');
