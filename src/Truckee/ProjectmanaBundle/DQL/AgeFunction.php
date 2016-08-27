<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DQL\AgeFunction.php

namespace Truckee\ProjectmanaBundle\DQL;

use Truckee\ProjectmanaBundle\DQL\SingleParameterFunction;
/**
 * AgeFunction
 *
 */
class AgeFunction extends SingleParameterFunction
{
    public $functionName = 'AGE';
}
