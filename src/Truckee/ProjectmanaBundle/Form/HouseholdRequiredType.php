<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Truckee\ProjectmanaBundle\Form\Field\CenterEnabledChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Truckee\ProjectmanaBundle\Form\Field\NoYesChoiceType;

class HouseholdRequiredType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('center', CenterEnabledChoiceType::class, array(
                    'label' => 'Site ',
                ))
                ->add('compliance', NoYesChoiceType::class, array(
                ))
                ->add('complianceDate', DateType::class, array(
                    'attr' => [
                        'placeholder' => 'mm/dd/yyyy',
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('shared', NoYesChoiceType::class, array(
                ))
                ->add('sharedDate', DateType::class, array(
                    'attr' => [
                        'placeholder' => 'mm/dd/yyyy',
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Household',
            'required' => false,
        ));
    }
}
