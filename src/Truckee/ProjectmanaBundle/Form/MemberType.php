<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src\Truckee\ProjectmanaBundle\Form\MemberType.php

namespace Truckee\ProjectmanaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Truckee\ProjectmanaBundle\Form\Field\DobAgeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Truckee\ProjectmanaBundle\Form\Field\YesNoChoiceType;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fname', TextType::class, array(
                'label' => 'First name:',
            ))
            ->add('sname', TextType::class, array(
                'label' => 'Last name:',
            ))
            ->add('dob', DobAgeType::class, array(
                'label' => 'DOB or age:',
            ))
            ->add(
                'sex',
                ChoiceType::class,
                array(
                'label' => 'Gender:',
                'placeholder' => 'Select gender',
                'choices' => array('Male' => 'Male', 'Female' => 'Female'),
            )
            )
            ->add(
                'ethnicity',
                EntityType::class,
                array(
                'label' => 'Ethnicity:',
                'class' => 'TruckeeProjectmanaBundle:Ethnicity',
                'choice_label' => 'abbreviation',
                'expanded' => false,
                'placeholder' => 'Select ethnicity',
                'attr' => (in_array('Ethnicity', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if (false === in_array('Ethnicity', $options['disabledOptions'])) {
                        return $er->createQueryBuilder('alias')
                        ->where('alias.enabled = 1')
                        ->orderBy('alias.abbreviation', 'ASC');
                    } else {
                        return $er->createQueryBuilder('alias')
                        ->orderBy('alias.abbreviation', 'ASC');
                    }
                },
            )
            )
            ->add('include', YesNoChoiceType::class, array(
                'label' => 'Include? ',
            ))
            ->add('excludeDate', DateType::class, array(
            ))
            ->add(
                'offences',
                EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Offence',
                'label' => 'Offense:',
                'choice_label' => 'offence',
                'label' => 'Offenses: ',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-inline',
                ],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if (false === in_array('offences', $options['disabledOptions'])) {
                        return $er->createQueryBuilder('e')
                        ->orderBy('e.offence', 'ASC')
                        ->where('e.enabled=1');
                    } else {
                        return $er->createQueryBuilder('e')
                        ->orderBy('e.offence', 'ASC');
                    }
                },
            )
            )
            ->add(
                'relation',
                EntityType::class,
                array(
                'label' => 'Relationship:',
                'class' => 'TruckeeProjectmanaBundle:Relationship',
                'choice_label' => 'relation',
                'expanded' => false,
                'placeholder' => 'Select relation to head',
                'attr' => (in_array('Relation', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if (false === in_array('Relation', $options['disabledOptions'])) {
                        return $er->createQueryBuilder('alias')
                        ->where('alias.enabled = 1')
                        ->orderBy('alias.relation', 'ASC');
                    } else {
                        return $er->createQueryBuilder('alias')
                        ->orderBy('alias.relation', 'ASC');
                    }
                },
            )
            )
            ->add(
                'isHead',
                CheckboxType::class,
                array(
                'mapped' => false,
                'label' => 'Head? ',
            )
            )
            ->add(
                'work',
                EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Work',
                'choice_label' => 'work',
                'placeholder' => 'Select work',
                'label' => 'Work:',
                'attr' => (in_array('Work', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if (false === in_array('Work', $options['disabledOptions'])) {
                        return $er->createQueryBuilder('alias')
                        ->where('alias.enabled = 1')
                        ->orderBy('alias.work', 'ASC');
                    } else {
                        return $er->createQueryBuilder('alias')
                        ->orderBy('alias.work', 'ASC');
                    }
                },
            )
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Member',
            'required' => false,
            'disabledOptions' => [],
            'fieldOptions' => [],
        ));
    }
}
