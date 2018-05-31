<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Validator\Constraints\DOB.php

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Description of DOB
 * @Annotation
 *
 * @author George
 */
class DOB extends Constraint
{
    public $message = 'Not a valid DOB';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
