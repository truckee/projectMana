<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ComplianceDateValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');

        $compliance = $data->getCompliance();
        $complianceDate = $data->getComplianceDate();
        // $complianceDate required only if $compliance = 'Yes'
        if (( 1 === $compliance && !$this->validateDate($complianceDate))) {

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
