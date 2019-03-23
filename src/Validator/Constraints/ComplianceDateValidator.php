<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Validator\Constraints;

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
