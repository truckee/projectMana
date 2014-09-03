<?php
namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates start date before end date
 * @Annotation
 */
class ComplianceDate extends Constraint

{
    public $message = 'Compliance date required';
    public $compliance;
    public $complianceDate;
}
?>
