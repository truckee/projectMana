<?php

// src\Mana\ClientBundle\Resources\views\Form\Field\MonthType.php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MonthType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {

        $resolver->setDefaults(array(
            'choices' => array(
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            )
        ));
    }

    public function getParent() {
        return ChoiceType::class;
    }

}

?>
