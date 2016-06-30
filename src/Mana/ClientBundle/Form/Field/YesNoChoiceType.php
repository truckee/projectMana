<?php
/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Form\Field\YesNoChoiceType.php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * YesNoChoiceType.
 */
class YesNoChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array('Yes' => '1', 'No' => '0'),
            'choices_as_values' => true,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
