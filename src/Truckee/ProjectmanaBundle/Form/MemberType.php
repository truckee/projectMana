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
                ->add('sex', ChoiceType::class, array(
                    'label' => 'Gender:',
                    'placeholder' => 'Select gender',
                    'choices' => array('Male' => 'Male', 'Female' => 'Female'),
                    'choices_as_values' => true,
                    ))
                ->add('ethnicity', EntityType::class, array(
                    'label' => 'Ethnicity:',
                    'class' => 'TruckeeProjectmanaBundle:Ethnicity',
                    'choice_label' => 'abbreviation',
                    'expanded' => false,
                    'placeholder' => 'Select ethnicity',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.abbreviation', 'ASC')
                        ;
                    },
                ))
                ->add('include', YesNoChoiceType::class, array(
                    'label' => 'Include? ',
                ))
                ->add('excludeDate', DateType::class, array(
                ))
                ->add('offences', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:Offence',
                    'label' => 'Offense:',
                    'choice_label' => 'offence',
                    'label' => 'Offenses: ',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.offence', 'ASC')
                                ->where('e.enabled=1');
                    },
                ))
                ->add('relation', EntityType::class, array(
                    'label' => 'Relationship:',
                    'class' => 'TruckeeProjectmanaBundle:Relationship',
                    'choice_label' => 'relation',
                    'expanded' => false,
                    'placeholder' => 'Select relation to head',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                                ->orderBy('r.relation', 'ASC')
                                ->where('r.enabled=1');
                    },
                ))
                ->add('isHead', CheckboxType::class, array(
                    'mapped' => false,
                    'label' => 'Head? ',
                ))
                ->add('work', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:Work',
                    'choice_label' => 'work',
                    'placeholder' => 'Select work',
                    'label' => 'Work:',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('w')
                                ->orderBy('w.work', 'ASC')
                                ->where('w.enabled=1');
                    },
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Member',
            'required' => false,
        ));
    }
}
