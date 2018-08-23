<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * require 3 digit area code.
 *
 * @Annotation
 */
class AreaCode extends Constraint
{
    public $message = 'Area code must be 3 digits';
}
