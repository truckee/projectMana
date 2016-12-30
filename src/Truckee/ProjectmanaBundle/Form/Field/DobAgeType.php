<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Mana/ClientBundle/Form/Type/DobAgeType.php
// the custom field to convert age to date of birth
namespace Truckee\ProjectmanaBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Truckee\ProjectmanaBundle\Form\DataTransformer\AgeToDOB;

class DobAgeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'invalid_message' => 'DOB or age not valid',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AgeToDOB();

        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
