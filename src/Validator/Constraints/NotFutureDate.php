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
 * Validates not future date.
 *
 * @Annotation
 */
class NotFutureDate extends Constraint
{
    public $message = 'Date may not be in future';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
