<?php
namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates not future date
 * @Annotation
 */
class AreaCode extends Constraint

{
    public $message = 'Area code must be 3 digits';

}
?>
