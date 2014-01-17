<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ArrivalYearValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint) {

        if ($value < 1930 || $value > Date('Y')) {

            $this->context->addViolation($constraint->message, array('%string%' => $value));

            return false;
        }

        return true;
    }
}

?>
