<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mana\ClientBundle\Form\MemberType;
use Mana\ClientBundle\Form\AddressType;
use Mana\ClientBundle\Form\PhoneType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;

class HouseholdType extends AbstractType {

    private $idArray;
    private $service;

    public function __construct($service, $idArray = null) {
        $this->idArray = $idArray;
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('active', 'choice', array(
                    'choices' => array('1' => 'Yes', '0' => 'No'),
                    'data' => 1,
                ))
                ->add('addresses', 'collection', array(
                    'type' => new AddressType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_name' => '__address__',
                ))
                ->add('appliances', 'entity', array(
                    'class' => 'ManaClientBundle:Appliance',
                    'property' => 'appliance',
                    'label' => 'Appliances: ',
                    'expanded' => true,
                    'multiple' => true,
                    'empty_value' => false,
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('a')
                        ->orderBy('a.appliance', 'ASC')
                        ->where("a.enabled=1");
            },
                ))
                ->add('arrivalmonth', new Type\MonthType(), array(
                    'empty_value' => false,
                ))
                ->add('arrivalyear', new Type\YearType(), array(
                    'empty_value' => false,
                ))
            ->add('center', 'entity', array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'constraints' => array(new NotBlank(array('message' => 'First site not selected')),),
                    'empty_value' => 'Select first site',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC')
                                ;
                    },
                ))
                ->add('compliance', 'choice', array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('complianceDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('dateAdded', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('foodStamps', 'choice', array(
                    'choices' => array('0' => 'No', '1' => 'Yes', '2' => 'Appl.'),
                    'empty_value' => '',
                ))
                ->add('fsamount', 'entity', array(
                    'class' => 'ManaClientBundle:FsAmount',
                    'property' => 'amount',
                    'label' => 'If yes, how much?',
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('f')
                        ->orderBy('f.amount', 'ASC')
                        ->where("f.enabled=1");
            },
                ))
                ->add('headId', 'hidden', array(
                    'mapped' => false,
                ))
                ->add('housing', 'entity', array(
                    'class' => 'ManaClientBundle:Housing',
                    'property' => 'housing',
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('h')
                        ->orderBy('h.housing', 'ASC')
                        ->where("h.enabled=1");
            },
                ))
                ->add('income', 'entity', array(
                    'class' => 'ManaClientBundle:Income',
                    'property' => 'income',
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('i')
                        ->where("i.enabled=1");
            },
                ))
                ->add('incomeSource', 'entity', array(
                    'class' => 'ManaClientBundle:IncomeSource',
                    'property' => 'incomeSource',
                    'label' => 'Income sources: ',
                    'expanded' => true,
                    'multiple' => true,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('i')
                        ->orderBy('i.incomeSource', 'ASC')
                        ->where("i.enabled=1");
            },
                ))
                ->add('isHead', 'choice', array(
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $this->idArray,
                ))
                ->add('members', 'collection', array(
                    'type' => new MemberType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'error_bubbling' => false,
                    'cascade_validation' => true,
                    'prototype' => true,
                ))
                ->add('notfoodstamp', 'entity', array(
                    'class' => 'ManaClientBundle:Notfoodstamp',
                    'label' => 'If no, why not? ',
                    'property' => 'notfoodstamp',
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('f')
                        ->orderBy('f.notfoodstamp', 'ASC')
                        ->where("f.enabled=1");
            },
                ))
                ->add('phones', 'collection', array(
                    'type' => new PhoneType(),
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false,
                ))
                ->add('reasons', 'entity', array(
                    'class' => 'ManaClientBundle:Reason',
                    'property' => 'reason',
                    'label' => 'Insufficient food: ',
                    'expanded' => true,
                    'multiple' => true,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('r')
                        ->orderBy('r.reason', 'ASC')
                        ->where("r.enabled=1");
            },
                ))
                ->add('shared', 'choice', array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('sharedDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if ($data['headId'] <> 0) {
                $newData = $this->service->replaceHeadData($data);
                $event->setData($newData);
            }
        });
    }

    public function getName() {
        return 'household';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'error_bubbling' => false,
            'cascade_validation' => true,
            'csrf_protection' => false,
            'required' => false,
            'attr' => array("class" => "smallform"),
        ));
    }

}
