<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

//use Symfony\Component\PropertyAccess\PropertyAccess;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        //        $now = new \DateTime();

        if (!(preg_match('/([0-9]{3})[ .-]([0-9]{4})/', $value) || empty($value))) {
            $this->context->addViolation($constraint->message);

            return false;
        }

        return true;
    }
}
