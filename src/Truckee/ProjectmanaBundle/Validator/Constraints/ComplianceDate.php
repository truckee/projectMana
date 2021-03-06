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
 * Validates start date before end date.
 *
 * @Annotation
 */
class ComplianceDate extends Constraint
{
    public $message = 'Compliance date required';
    public $compliance;
    public $complianceDate;
}
