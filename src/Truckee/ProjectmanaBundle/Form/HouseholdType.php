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
use Truckee\ProjectmanaBundle\Form\Field\MonthType;
use Truckee\ProjectmanaBundle\Form\Field\NoYesChoiceType;
use Truckee\ProjectmanaBundle\Form\PhysicalAddressType;
use Truckee\ProjectmanaBundle\Form\MailingAddressType;
use Truckee\ProjectmanaBundle\Form\Field\YearType;
use Truckee\ProjectmanaBundle\Form\Field\YesNoChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HouseholdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
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
            ->add('addresses', CollectionType::class,
                array(
                'entry_type' => AddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ))
            ->add('arrivalmonth', MonthType::class,
                array(
                'placeholder' => false,
                'label' => 'Arrival month: ',
            ))
            ->add('arrivalyear', YearType::class,
                array(
                'placeholder' => false,
                'label' => 'Arrival year: ',
            ))
            ->add('center', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Center',
                'choice_label' => 'center',
                'placeholder' => 'Select site',
                'attr' => (in_array('Center', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                'query_builder' => function (EntityRepository $er) use ($options) {
                if (false === in_array('Center', $options['disabledOptions'])) {
                    return $er->createQueryBuilder('c')
                        ->where('c.enabled = 1')
                        ->orderBy('c.center', 'ASC');
                } else {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.center', 'ASC');
                }
            },
                'constraints' => array(new NotBlank(array('message' => 'Site must be selected'))),
            ))
            ->add('compliance', NoYesChoiceType::class,
                array(
                'choices_as_values' => true,
                'label' => 'Compliance: ',
            ))
            ->add('complianceDate', DateType::class,
                array(
                'label' => 'Compliance date: ',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'M/d/y',
            ))
            ->add('foodstamp', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:FsStatus',
                'choice_label' => 'status',
                'placeholder' => '',
                'label' => 'Food stamp status: ',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.id', 'ASC')
                    ;
                },
            ))
            ->add('fsamount', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:FsAmount',
                'choice_label' => 'amount',
                'label' => 'If foodstamps, how much?',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.amount', 'ASC')
                        ->where('f.enabled=1');
                },
            ))
            ->add('housing', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Housing',
                'choice_label' => 'housing',
                'placeholder' => '',
                'attr' => (in_array('Housing', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                'label' => 'Housing: ',
                'query_builder' => function (EntityRepository $er) use ($options) {
                if (false === in_array('Housing', $options['disabledOptions'])) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.housing', 'ASC')
                        ->where('h.enabled=1');
                } else {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.housing', 'ASC');
                }
            },
            ))
            ->add('income', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Income',
                'choice_label' => 'income',
                'placeholder' => '',
                'label' => 'Income bracket: ',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->where('i.enabled=1');
                },
            ))
            ->add('notfoodstamp', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Notfoodstamp',
                'label' => 'If not food stamps, why not? ',
                'choice_label' => 'notfoodstamp',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.notfoodstamp', 'ASC')
                        ->where('f.enabled=1');
                },
            ))
            ->add('phones', CollectionType::class,
                array(
                'entry_type' => PhoneType::class,
                'label' => 'Phone: ',
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false,
            ))
            ->add('reasons', EntityType::class,
                array(
                'class' => 'TruckeeProjectmanaBundle:Reason',
                'choice_label' => 'reason',
                'label' => 'Insufficient food: ',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-inline',
                ],
                'query_builder' => function (EntityRepository $er ) use($options) {
                if (false === in_array('reasons', $options['disabledOptions'])) {
                return $er->createQueryBuilder('r')
                    ->orderBy('r.reason', 'ASC')
                    ->where('r.enabled=1');
                } else {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.reason', 'ASC');
                }
            },
            ))
            ->add('shared', NoYesChoiceType::class, array(
                'label' => 'Shared: ',
            ))
            ->add('sharedDate', DateType::class,
                array(
                'label' => 'Shared date: ',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'M/d/y',
            ))
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
