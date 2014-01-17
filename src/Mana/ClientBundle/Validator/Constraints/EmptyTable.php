<?php

namespace Mana\ClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates start date before end date
 * @Annotation
 */
class EmptyTable extends Constraint {

    public $message = 'No data exists for these dates';
    public $startMonth;
    public $startYear;

    public function validatedBy() {
        return 'empty_table';
    }
}

?>
