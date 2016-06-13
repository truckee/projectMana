<?php

namespace Mana\ClientBundle\Form;

use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Form\AddressType;
use Mana\ClientBundle\Form\PhoneType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Mana\ClientBundle\Utilities\DisabledOptions;

class HouseholdType extends AbstractType
{

    private $new;

    public function __construct($new = null) {
        $this->new = $new;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $new = $this->new;
        $builder
                ->add('active', ChoiceType::class, array(
                    'choices' => array('1' => 'Yes', '0' => 'No'),
                    'label' => 'Active: ',
                    'attr' => $options['attr'],
                ))
                ->add('addresses', CollectionType::class, array(
                    'entry_type' => AddressType::class,
                    'attr' => $options['attr'],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                ))
                ->add('arrivalmonth', new Field\MonthType(), array(
                    'placeholder' => false,
                    'label' => 'Arrival month: ',
                    'attr' => $options['attr'],
                ))
                ->add('arrivalyear', new Field\YearType(), array(
                    'placeholder' => false,
                    'label' => 'Arrival year: ',
                    'attr' => $options['attr'],
                ))
                ->add('compliance', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                    'label' => 'Compliance: ',
                    'attr' => $options['attr'],
                ))
                ->add('complianceDate', DateType::class, array(
                    'label' => 'Compliance date: ',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => $options['attr'],
                ))
//                ->add('dateAdded', DateType::class, array(
//                    'widget' => 'single_text',
//                    'format' => 'MM/dd/yyyy',
//                ))
                ->add('foodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:FsStatus',
                    'choice_label' => 'status',
                    'placeholder' => '',
                    'label' => 'Food stamp status: ',
                    'attr' => $options['attr'],
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
                    'attr' => $options['attr'],
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
                    'attr' => $options['attr'],
                    'query_builder' => function(EntityRepository $er) use($new) {
                        if (true === $new) {
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
                    'attr' => $options['attr'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->where("i.enabled=1");
                    },
                ))
//                ->add('incomeSource', EntityType::class, array(
//                    'class' => 'ManaClientBundle:IncomeSource',
//                    'choice_label' => 'incomeSource',
//                    'label' => 'Income sources: ',
//                    'expanded' => true,
//                    'multiple' => true,
//                    'placeholder' => '',
//                    'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('i')
//                                ->orderBy('i.incomeSource', 'ASC')
//                                ->where("i.enabled=1");
//                    },
//                ))
                ->add('notfoodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:Notfoodstamp',
                    'label' => 'If not food stamps, why not? ',
                    'choice_label' => 'notfoodstamp',
                    'placeholder' => '',
                    'attr' => $options['attr'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.notfoodstamp', 'ASC')
                                ->where("f.enabled=1");
                    },
                ))
                ->add('phones', CollectionType::class, array(
                    'entry_type' => new PhoneType(),
                    'label' => 'Phone: ',
                    'attr' => $options['attr'],
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
                ->add('shared', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                    'label' => 'Shared: ',
                    'attr' => $options['attr'],
                ))
                ->add('sharedDate', DateType::class, array(
                    'label' => 'Shared date: ',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => $options['attr'],
                ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $household = $event->getData();
            $form = $event->getForm();
            $new = $this->new;
            if (true === $new || empty($household->getCenter())) {
                $form->add('center', new Field\CenterEnabledChoiceType());
            } else {
                $form->add('center', new Field\CenterAllChoiceType());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'required' => false,
//            'attr' => [
//                'style' => 'margin: 0 30px 0 10px;'
//            ],
        ));
    }

}
