<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src\App\Form\MemberType.php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('fname', TextType::class, [
                    'attr' => [
                        'placeholder' => 'First name',
                    ],
                ])
                ->add('sname', TextType::class, [
                    'attr' => [
                        'placeholder' => 'Last name',
                    ],
                ])
                ->add('dob', TextType::class, [
                    'attr' => [
                        'placeholder' => 'DOB or age',
                    ],
                    'invalid_message' => 'Invalid date or age entry'
                ])
                ->add(
                    'sex',
                    ChoiceType::class,
                    array(
                            'label' => 'Gender',
                            'placeholder' => 'Select gender',
                            'choices' => array('Female' => 'Female', 'Male' => 'Male', 'Other' => 'Other'),
                        )
                )
                ->add(
                    'ethnicity',
                    EntityType::class,
                    array(
                            'label' => 'Ethnicity',
                            'class' => 'App:Ethnicity',
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
                ->add('include', ChoiceType::class, array(
                    'choices' => ['Yes' => '1', 'No' => '0'],
                    'label' => 'Include? ',
                ))
//                ->add('excludeDate', DateType::class, array(
//                ))
                ->add(
                    'relation',
                    EntityType::class,
                    array(
                            'label' => 'Relationship:',
                            'class' => 'App:Relationship',
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
                    'jobs',
                    EntityType::class,
                    array(
                            'class' => 'App:Work',
                            'choice_label' => 'job',
                            'placeholder' => 'Select work',
                            'label' => 'Work:',
                            'expanded' => true,
                            'multiple' => true,
                            'attr' => (in_array('Work', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Work', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.job', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.job', 'ASC');
                                }
                            },
                        )
                )
                ->add('submit', SubmitType::class)
        ;

        $builder->get('dob')
                ->addModelTransformer(new CallbackTransformer(
                    function ($dobAsObject) {
                        if (null === $dobAsObject) {
                            return;
                        }
                        return date_format($dobAsObject, 'm/d/Y');
                    },
                    function ($dobAsString) {
                        if (!is_numeric($dobAsString) && substr_count($dobAsString, '/') === 0) {
                            throw new TransformationFailedException();
                        }
                        if (is_numeric($dobAsString) && 0 > $dobAsString) {
                            throw new TransformationFailedException();
                        }
                        if (is_numeric($dobAsString) && $dobAsString > 120) {
                            throw new TransformationFailedException();
                        }
                        if (substr_count($dobAsString, '/') === 0) {
                            $dob = new \DateTime();
                            $dob->sub(new \DateInterval('P' . $dobAsString . 'Y'));
                        } else {
                            $dob = new \DateTime($dobAsString);
                        }
                        $date = new \DateTime();
                        $lowerLimit = $date->sub(new \DateInterval('P120Y'));
                        if ($dob < new \DateTime() && $dob > $lowerLimit) {
                            return $dob;
                        } else {
                            throw new TransformationFailedException();
                        }
                    }
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Member',
            'required' => false,
            'disabledOptions' => [],
            'fieldOptions' => [],
        ));
    }
}
