<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates start date before end date.
 *
 * @Annotation
 */
class StartEndDate extends Constraint
{
    public $message = 'End date must be same or later than start date';
    public $startMonth;
    public $startYear;
    public $endMonth;
    public $endYear;
}
