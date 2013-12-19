<?php

// src\Mana\ClientBundle\Resources\views\Form\Type\MonthType.php

namespace Mana\ClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MonthType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

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

    public function getName() {
        return 'month_names';
    }

    public function getParent() {
        return 'choice';
    }

}

?>
