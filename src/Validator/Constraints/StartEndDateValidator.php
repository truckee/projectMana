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

class StartEndDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');

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
