<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Validator\Constraints\DOBValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Description of DOBValidator
 *
 * @author George
 */
class DOBValidator extends ConstraintValidator
{
    public function validate($member, Constraint $constraint)
    {
        $isNewHousehold = is_null($this->context->getValue()->getHousehold());
        if (true === $isNewHousehold) {
            $now = new \DateTime();
            if ($now->sub(new \DateInterval('P5Y')) < $member->getDob()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('dob')
                    ->addViolation();
            }
        }
    }
}
