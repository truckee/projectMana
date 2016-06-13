<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Description of ValidGroupAwareValidator
 *
 * @author George
 */
class ValidGroupAwareValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof ValidGroupAware) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\ValidGroupAware');
        }

        $violations = $this->context->getValidator()->validate($value, [new Valid()], [$this->context->getGroup()]);
        /** @var ConstraintViolation[] $violations */
        foreach ($violations as $violation) {
            $this->context->buildViolation($violation->getMessage())
                    ->setParameters($violation->getParameters())
                    ->setCode($violation->getCode())
                    ->setCause($violation->getCause())
                    ->setPlural($violation->getPlural())
                    ->setInvalidValue($violation->getInvalidValue())
                    ->atPath($violation->getPropertyPath())
                    ->addViolation();
        }
    }

}
