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
$I->wantTo('Edit several members V1 household');
$I->loginAsAdmin();
$I->fillField('qtext', 'MoreThanOne Member');
$I->click('Search');
$I->see('Household View');
$I->click('Edit household');
$I->see('select new head of household from included members');
$I->click('Submit');
$I->see('select new head of household from included members');
