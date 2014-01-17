<?php
namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;
 
class CenterOrCountyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($this->context->getRoot(), 'data');
        
        if (!empty($data['county_id']) && !empty($data['center_id'])) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
            return false;
        }
 
        return true;
    }
}
?>
