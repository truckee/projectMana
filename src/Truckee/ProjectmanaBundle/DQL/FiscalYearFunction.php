<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DQL\FiscalYear.php

namespace Truckee\ProjectmanaBundle\DQL;

use Truckee\ProjectmanaBundle\DQL\SingleParameterFunction;

/**
 * FiscalYearFunction ::= "FY" "(" ArithmeticPrimary ")"
 *
 */
class FiscalYearFunction extends SingleParameterFunction
{
    public $functionName = 'FY';
}
