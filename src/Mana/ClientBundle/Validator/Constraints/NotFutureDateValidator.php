<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

//use Symfony\Component\PropertyAccess\PropertyAccess;

class NotFutureDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $now = new \DateTime();

        if (!($value <  $now)) {
            $this->context->addViolation($constraint->message);

            return false;
        }

        return true;
    }
}
