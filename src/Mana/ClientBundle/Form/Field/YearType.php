<?php

// src\Mana\ClientBundle\Resources\views\Form\Field\YearType.php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class YearType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $range = range(Date('Y'), 1930, -1);
        $years = array();
        foreach ($range as $year) {
            $years[$year] = $year;
        }
        $resolver->setDefaults(array(
            'choices' => $years
        ));
    }

    public function getName() {
        return 'year_names';
    }

    public function getParent() {
        return 'choice';
    }

}

?>
