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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Truckee\ProjectmanaBundle\Form\Field\CenterEnabledChoiceType;
use Truckee\ProjectmanaBundle\Form\Field\MonthType;
use Truckee\ProjectmanaBundle\Form\Field\NoYesChoiceType;
use Truckee\ProjectmanaBundle\Form\Field\YearType;

class HouseholdRequiredType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('center', CenterEnabledChoiceType::class, [
                'label' => 'Site:',
                ])
            ->add(
                'arrivalmonth', MonthType::class, array(
                    'placeholder' => false,
                    'label' => 'Arrival&nbsp;month: ',
                )
            )
            ->add(
                'arrivalyear', YearType::class, array(
                    'placeholder' => false,
                    'label' => 'Arrival&nbsp;year: ',
                    )
            )
            ->add(
                'compliance', NoYesChoiceType::class, array(
                'label' => 'Compliance: ',
                )
            )
            ->add(
                'complianceDate', DateType::class,
                array(
                    'label' => 'Compliance&nbsp;date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                )
            )
            ->add('shared', NoYesChoiceType::class, array(
                'label' => 'Shared: ',
            ))
            ->add(
                'sharedDate', DateType::class,
                array(
                    'label' => 'Shared&nbsp;date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                )
            )
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
