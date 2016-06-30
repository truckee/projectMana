<?php
// src\Mana\ClientBundle\Resources\views\Form\Field\YearType.php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class YearType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $range = range(Date('Y'), Date('Y') - 50, -1);
        $years = array();
        foreach ($range as $year) {
            $years[$year] = $year;
        }
        $resolver->setDefaults(array(
            'choices' => $years,
            'choices_as_values' => true,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
