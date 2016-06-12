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
$I->see('Select');
$I->click('Select');
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

$I->click('Close');
$I->click('#memberId3');
$I->waitForJS("return $.active == 0;", 2);
$I->see('Edit household member');
$I->see('Include?');

$I->click('#member_isHead');
$I->waitForJS("return $.active == 0;", 1);
$I->dontSee('Include?');

$I->fillField("#member_dob", 12);
$I->selectOption('form select[name="member[sex]"]', 'Female');
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 2);
$I->see('has been updated');

$I->click('Close');
$isHead = $I->grabTextFrom('span#include3');
$I->assertTrue('Head' === $isHead);
$style = $I->grabAttributeFrom('#row3', 'style');
$I->assertNotEmpty($style);
$I->dontSeeElement('#row2', ['style']);
$isIncluded = $I->grabTextFrom('span#include2');
$I->assertContains('Include', $isIncluded);

$I->click('#memberId5');
$I->waitForJS("return $.active == 0;", 2);
$I->selectOption('#member_include', 'No');
$I->click('button#submit');
$I->waitForJS("return $.active == 0;", 2);
$I->click('Close');
$I->waitForJS("return $.active == 0;", 2);
$I->see('Excluded');
