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

use Doctrine\ORM\EntityRepository;
use Truckee\ProjectmanaBundle\Form\Field\NoYesChoiceType;
use Truckee\ProjectmanaBundle\Form\PhysicalAddressType;
use Truckee\ProjectmanaBundle\Form\MailingAddressType;
use Truckee\ProjectmanaBundle\Form\Field\YesNoChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseholdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', YesNoChoiceType::class, array(
                'label' => 'Active: ',
            ))
            ->add('physicalAddress', PhysicalAddressType::class, [
                'mapped' => false,
            ])
            ->add('mailingAddress', MailingAddressType::class, [
                'mapped' => false,
            ])
            ->add(
                'addresses', CollectionType::class,
                array(
                    'entry_type' => AddressType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                )
            )
            ->add(
                'center', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Center',
                    'label' => 'Site:',
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
                'compliance', NoYesChoiceType::class, array(
                    'label' => 'Compliance: ',
                )
            )
            ->add(
                'complianceDate', DateType::class,
                array(
                    'label' => 'Compliance date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                )
            )
            ->add(
                'foodstamp', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:FsStatus',
                    'choice_label' => 'status',
                    'placeholder' => '',
                    'label' => 'Food stamp status: ',
                    'attr' => (in_array('Foodstamp', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        if (false === in_array('Foodstamp', $options['disabledOptions'])) {
                            return $er->createQueryBuilder('alias')
                                ->where('alias.enabled = 1')
                                ->orderBy('alias.status', 'ASC');
                        } else {
                            return $er->createQueryBuilder('alias')
                                ->orderBy('alias.status', 'ASC');
                        }
                    }
                )
            )
            ->add(
                'fsamount', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:FsAmount',
                    'choice_label' => 'amount',
                    'label' => 'If foodstamps, how much?',
                    'placeholder' => '',
                    'attr' => (in_array('FsAmount', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        if (false === in_array('FsAmount', $options['disabledOptions'])) {
                            return $er->createQueryBuilder('alias')
                                ->where('alias.enabled = 1')
                                ->orderBy('alias.amount', 'ASC');
                        } else {
                            return $er->createQueryBuilder('c')
                                ->orderBy('alias.amount', 'ASC');
                        }
                    })
            )
            ->add(
                'housing', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Housing',
                    'choice_label' => 'housing',
                    'placeholder' => '',
                    'attr' => (in_array('Housing', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                    'label' => 'Housing: ',
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
                'income', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Income',
                    'choice_label' => 'income',
                    'placeholder' => '',
                    'label' => 'Income bracket: ',
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
                'notfoodstamp', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Notfoodstamp',
                    'label' => 'If not food stamps, why not? ',
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
                'phones', CollectionType::class,
                array(
                    'entry_type' => PhoneType::class,
                    'label' => 'Phone: ',
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false,
                )
            )
            ->add(
                'reasons', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Reason',
                    'choice_label' => 'reason',
                    'label' => 'Insufficient food: ',
                    'expanded' => true,
                    'multiple' => true,
//                'attr' => [
//                    'class' => 'form-inline',
//                ],
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
            ->add('shared', NoYesChoiceType::class, array(
                'label' => 'Shared: ',
            ))
            ->add(
                'sharedDate', DateType::class,
                array(
                    'label' => 'Shared date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                )
            )
            ->add(
                'assistances', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Assistance',
                    'choice_label' => 'assistance',
                    'placeholder' => '',
                    'label' => 'Seeking services: ',
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
            ->add('seeking', TextType::class, [
                'label' => 'Seeking service:'
            ])
            ->add(
                'organizations', EntityType::class,
                array(
                    'class' => 'TruckeeProjectmanaBundle:Organization',
                    'choice_label' => 'organization',
                    'placeholder' => '',
                    'label' => 'Receiving services: ',
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
            ->add('receiving', TextType::class, [
                'label' => 'Receiving service from:'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Household',
            'required' => false,
            'disabledOptions' => [],
        ));
    }
}
