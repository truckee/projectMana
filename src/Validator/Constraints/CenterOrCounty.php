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
 * Validates center(rid) with respect to ctyId.
 *
 * @Annotation
 */
class CenterOrCounty extends Constraint
{
    public $message = 'Either county or center but not both';
    public $ctyId;
    public $rid;
}
