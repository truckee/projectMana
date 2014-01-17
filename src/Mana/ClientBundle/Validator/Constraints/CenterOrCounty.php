<?php
namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates center(rid) with respect to ctyId
 * @Annotation
 */
class CenterOrCounty extends Constraint

{
    public $message = 'Either county or center but not both';
    public $ctyId;
    public $rid;
}
?>
