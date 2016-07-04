<?php

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

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
