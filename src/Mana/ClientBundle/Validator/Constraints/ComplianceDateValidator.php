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
        $isDateObj = is_object($complianceDate);
        if ('1' === $compliance && !$isDateObj) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));

            return false;
        }

        return true;
    }
}
