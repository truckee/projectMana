<?php

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates not future date.
 *
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public $message = 'Phone # must be xxx-yyyy';
}
