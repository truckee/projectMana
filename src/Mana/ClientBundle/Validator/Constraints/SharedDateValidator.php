<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SharedDateValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');

        $shared = $data->getShared();
        $shareddate = $data->getSharedDate();
        $isDateObj = is_object($shareddate);
        if ( '1' === $shared && !$isDateObj ) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
            return false;
        }

        return true;
    }
}
