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
$I->wantTo('create a new household with existing name');
$I->loginAsAdmin();
$I->click('New household');
$I->fillField('household[members][0][fname]', 'Benny');
$I->fillField('household[members][0][sname]', 'Borko');
$I->fillField('household[members][0][dob]', '44');
$I->selectOption('household[members][0][sex]', 'Male');
$I->selectOption('household[members][0][ethnicity]', 'Cau');
$I->selectOption('household[center]', 'Truckee');
$I->fillField('household[complianceDate]', '2/15/2016');
$I->fillField('household[sharedDate]', '2/15/2016');
$I->click('Submit');
$I->see('Add new head of house or view existing households');
