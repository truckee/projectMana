<?php

namespace Mana\ClientBundle\Form;

use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Form\AddressType;
use Mana\ClientBundle\Form\Field\CenterAllChoiceType;
use Mana\ClientBundle\Form\Field\CenterEnabledChoiceType;
use Mana\ClientBundle\Form\Field\MonthType;
use Mana\ClientBundle\Form\Field\NoYesChoiceType;
use Mana\ClientBundle\Form\Field\YearType;
use Mana\ClientBundle\Form\Field\YesNoChoiceType;
use Mana\ClientBundle\Form\PhoneType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseholdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->new = $options['new'];
        $builder
                ->add('active', YesNoChoiceType::class, array(
                    'label' => 'Active: ',
                ))
                ->add('addresses', CollectionType::class, array(
                    'entry_type' => AddressType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                ))
                ->add('arrivalmonth', MonthType::class, array(
                    'placeholder' => false,
                    'label' => 'Arrival month: ',
                ))
                ->add('arrivalyear', YearType::class, array(
                    'placeholder' => false,
                    'label' => 'Arrival year: ',
                ))
                ->add('compliance', NoYesChoiceType::class, array(
                    'choices_as_values' => true,
                    'label' => 'Compliance: ',
                ))
                ->add('complianceDate', DateType::class, array(
                    'label' => 'Compliance date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                ))
                ->add('foodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:FsStatus',
                    'choice_label' => 'status',
                    'placeholder' => '',
                    'label' => 'Food stamp status: ',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.id', 'ASC')
                        ;
                    },
                ))
                ->add('fsamount', EntityType::class, array(
                    'class' => 'ManaClientBundle:FsAmount',
                    'choice_label' => 'amount',
                    'label' => 'If foodstamps, how much?',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.amount', 'ASC')
                                ->where("f.enabled=1");
                    },
                ))
                ->add('housing', EntityType::class, array(
                    'class' => 'ManaClientBundle:Housing',
                    'choice_label' => 'housing',
                    'placeholder' => '',
                    'label' => 'Housing: ',
                    'query_builder' => function(EntityRepository $er) use($options) {
                        if (true === $options['new']) {
                            return $er->createQueryBuilder('h')
                                    ->orderBy('h.housing', 'ASC')
                                    ->where("h.enabled=1");
                        } else {
                            return $er->createQueryBuilder('h')
                                    ->orderBy('h.housing', 'ASC');
                        }
                    },
                ))
                ->add('income', EntityType::class, array(
                    'class' => 'ManaClientBundle:Income',
                    'choice_label' => 'income',
                    'placeholder' => '',
                    'label' => 'Income bracket: ',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->where("i.enabled=1");
                    },
                ))
                ->add('notfoodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:Notfoodstamp',
                    'label' => 'If not food stamps, why not? ',
                    'choice_label' => 'notfoodstamp',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.notfoodstamp', 'ASC')
                                ->where("f.enabled=1");
                    },
                ))
                ->add('phones', CollectionType::class, array(
                    'entry_type' => PhoneType::class,
                    'label' => 'Phone: ',
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false,
                ))
                ->add('reasons', EntityType::class, array(
                    'class' => 'ManaClientBundle:Reason',
                    'choice_label' => 'reason',
                    'label' => 'Insufficient food: ',
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'form-inline',
                    ],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                                ->orderBy('r.reason', 'ASC')
                                ->where("r.enabled=1");
                    },
                ))
                ->add('shared', NoYesChoiceType::class, array(
                    'label' => 'Shared: ',
                ))
                ->add('sharedDate', DateType::class, array(
                    'label' => 'Shared date: ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $household = $event->getData();
            $form = $event->getForm();
            if (true === $this->new || empty($household->getCenter())) {
                $form->add('center', CenterEnabledChoiceType::class);
            } else {
                $form->add('center', CenterAllChoiceType::class);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'required' => false,
            'new' => null,
        ));
    }

}
