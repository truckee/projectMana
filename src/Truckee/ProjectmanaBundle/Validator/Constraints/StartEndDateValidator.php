<?php

namespace Truckee\ProjectmanaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class StartEndDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');

//        $submitted = $this->context->getRoot()->getChildren();
        $startMonth = $data['startMonth'];
        $startYear = $data['startYear'];
        $endMonth = $data['endMonth'];
        $endYear = $data['endYear'];
        $startDate = new \DateTime($startMonth.'/01/'.$startYear);
        $endDate = new \DateTime($endMonth.'/01/'.$endYear);
        if (!($startDate <= $endDate)) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));

            return false;
        }

        return true;
    }
}
