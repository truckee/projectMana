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
        // $complianceDate required only if $compliance = 'Yes'
        if (( 1 === $shared && !$this->validateDate($shareddate))) {

            $this->context->addViolation($constraint->message, array('%string%' => $value));

            return false;
        }

        return true;
    }

    public function validateDate($date, $format = 'm/d/Y')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}

?>
