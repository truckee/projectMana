<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use App\Form\PhysicalAddressType;
use App\Form\MailingAddressType;
use App\Form\Field\MonthType;
use App\Form\Field\YearType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseholdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('active', ChoiceType::class, array(
                    'label' => 'Active: ',
                ))
                ->add('physicalAddress', PhysicalAddressType::class, [
                    'mapped' => false,
                ])
                ->add('mailingAddress', MailingAddressType::class, [
                    'mapped' => false,
                ])
                ->add(
                    'addresses',
                    CollectionType::class,
                    array(
                            'entry_type' => AddressType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'by_reference' => false,
                            'prototype' => true,
                        )
                )
                ->add(
                    'assistances',
                    EntityType::class,
                    array(
                            'class' => 'App:Assistance',
                            'choice_label' => 'assistance',
                            'placeholder' => '',
                            'label' => 'Q9: Seeking services: ',
                            'expanded' => true,
                            'multiple' => true,
                            'attr' => (in_array('Assistance', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Assistance', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.position, alias.assistance', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'arrivalmonth',
                    MonthType::class,
                    array(
                    'placeholder' => 'Arrival month',
                        )
                )
                ->add(
                    'arrivalyear',
                    YearType::class,
                    array(
                    'placeholder' => 'Arrival year',
                        )
                )
                ->add(
                    'center',
                    EntityType::class,
                    array(
                            'class' => 'App:Center',
                            'label' => 'First site:',
                            'choice_label' => 'center',
                            'placeholder' => 'Select site',
                            'attr' => (in_array('Center', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Center', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.center', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.center', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'compliance',
                    ChoiceType::class,
                    [
                    'choices' => ['No' => '0', 'Yes' => '1'],
                        ]
                )
                ->add(
                    'complianceDate',
                    DateType::class,
                    array(
                            'placeholder' => 'Compliance date: ',
                            'widget' => 'single_text',
                            'html5' => false,
                            'attr' => ['class' => 'js-datepicker'],
                            'format' => 'M/d/y',
                        )
                )
                ->add(
                    'benefits',
                    EntityType::class,
                    array(
                            'class' => 'App:Benefit',
                            'choice_label' => 'benefit',
                            'placeholder' => '',
                            'label' => 'Q4: Benefits: ',
                            'expanded' => true,
                            'multiple' => true,
                            'attr' => (in_array('Benefit', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Benefit', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.benefit', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'housing',
                    EntityType::class,
                    array(
                            'class' => 'App:Housing',
                            'choice_label' => 'housing',
                            'placeholder' => '',
                            'attr' => (in_array('Housing', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'label' => 'Q7: Housing: ',
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Housing', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.housing', 'ASC')
                                            ->where('alias.enabled=1');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.housing', 'ASC');
                                }
                            },
                        )
                )
                ->add(
                    'income',
                    EntityType::class,
                    array(
                            'class' => 'App:Income',
                            'choice_label' => 'income',
                            'placeholder' => '',
                            'label' => 'Q6: Income bracket: ',
                            'attr' => (in_array('Income', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Income', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.income', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.income', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'notfoodstamp',
                    EntityType::class,
                    array(
                            'class' => 'App:Notfoodstamp',
                            'label' => 'Q5: If not food stamps, why not? ',
                            'choice_label' => 'notfoodstamp',
                            'placeholder' => '',
                            'attr' => (in_array('Notfoodstamp', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Notfoodstamp', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.notfoodstamp', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.notfoodstamp', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'organizations',
                    EntityType::class,
                    array(
                            'class' => 'App:Organization',
                            'choice_label' => 'organization',
                            'placeholder' => '',
                            'label' => 'Q10: Receiving services: ',
                            'expanded' => true,
                            'multiple' => true,
                            'attr' => (in_array('Organization', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Organization', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.position, alias.organization', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'reasons',
                    EntityType::class,
                    array(
                            'class' => 'App:Reason',
                            'choice_label' => 'reason',
                            'label' => 'Q8: Insufficient food: ',
                            'expanded' => true,
                            'multiple' => true,
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('reasons', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.reason', 'ASC')
                                            ->where('alias.enabled=1');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.reason', 'ASC');
                                }
                            },
                        )
                )
                ->add('shared', ChoiceType::class, [
                    'choices' => array('No' => '0', 'Yes' => '1'),
                ])
                ->add(
                    'sharedDate',
                    DateType::class,
                    array(
                            'label' => 'Shared date: ',
                            'widget' => 'single_text',
                            'html5' => false,
                            'attr' => ['class' => 'js-datepicker'],
                            'format' => 'M/d/y',
                        )
                )
                //following two fields for when Other is checked
                ->add('seeking', TextType::class, [
                    'label' => 'Seeking service:'
                ])
                ->add('receiving', TextType::class, [
                    'label' => 'Receiving service from:'
                ])
                ->add('areacode', TextType::class, array(
                    'attr' => array('size' => 2),
                    'label' => 'Area code: ',
                ))
                ->add('phoneNumber', TextType::class, array(
                    'attr' => array('size' => 8),
                    'label' => 'Phone: ',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Household',
            'required' => false,
            'disabledOptions' => [],
        ));
    }
}
