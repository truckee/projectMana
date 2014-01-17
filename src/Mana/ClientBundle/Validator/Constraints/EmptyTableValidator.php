<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EmptyTableValidator extends ConstraintValidator {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint) {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');

        $startMonth = $data['startMonth'];
        $startYear = $data['startYear'];
        $endMonth = $data['endMonth'];
        $endYear = $data['endYear'];
        $startDate = new \DateTime($startMonth . '/01/' . $startYear);
        $endDate = new \DateTime($endMonth . '/01/' . $endYear);

        $contacts = $this->em->getRepository('ManaClientBundle:Contact')->dataExists($startText, $endText);

        if ($contacts === 0) {

            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }

}

?>
