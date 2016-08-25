<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
