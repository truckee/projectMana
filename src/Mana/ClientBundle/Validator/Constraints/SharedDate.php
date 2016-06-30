<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates start date before end date.
 *
 * @Annotation
 */
class SharedDate extends Constraint
{
    public $message = 'Shared date required';
    public $shared;
    public $shareddate;
}
