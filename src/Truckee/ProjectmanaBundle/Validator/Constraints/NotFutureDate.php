<?php

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates not future date.
 *
 * @Annotation
 */
class NotFutureDate extends Constraint
{
    public $message = 'Date may not be in future';
}
