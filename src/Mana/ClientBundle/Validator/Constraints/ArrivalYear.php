<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates start date before end date
 * @Annotation
 */
class ArrivalYear extends Constraint {

    public $message = 'Arrival year not valid';
    public $arrivalYear;

//    public function validatedBy() {
//        return 'arrival_year';
//    }
}

?>
