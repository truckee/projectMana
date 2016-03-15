<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mana\ClientBundle\Form\MemberType;
use Mana\ClientBundle\Form\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Mana\ClientBundle\Form\PhoneType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Utilities\DisabledOptions;

class HouseholdType extends AbstractType
{

    private $idArray;
    private $service;

    public function __construct($service, $idArray = null)
    {
        $this->idArray = $idArray;
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                ->add('arrivalmonth', new Field\MonthType(), array(
                    'empty_value' => false,
                ))
                ->add('arrivalyear', new Field\YearType(), array(
                    'empty_value' => false,
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
                ->add('foodstamp', 'entity', array(
                    'class' => 'ManaClientBundle:FsStatus',
                    'property' => 'status',
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.id', 'ASC')
                        ;
                    },
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
//                ->add('housing', EntityType::class, array(
//                    'class' => 'ManaClientBundle:Housing',
//                    'property' => 'housing',
//                    'empty_value' => '',
//                    'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('h')
//                                ->orderBy('h.housing', 'ASC')
//                                ->where("h.enabled=1");
//                    },
//                ))
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $household = $event->getData();
            $form = $event->getForm();
            $center = $household->getCenter();
            if (empty($center)) {
                $form->add('center', new Field\CenterEnabledChoiceType());
            }
            else {
                $form->add('center', new Field\CenterAllChoiceType());
            }

            $fieldEntity = $household->getHousing();
            $enabled = (null !== $fieldEntity) ? $fieldEntity->getEnabled() : true;
            if (null !== $fieldEntity && !$fieldEntity->getEnabled()) {
                $form->add('disabledHousing', 'text', array(
                    'data' => $fieldEntity->getHousing(),
                    'attr' => ['disabled' => true],
                    'label' => 'Housing: ',
                    'label_attr' => ['style' => 'font-weight:bold;'],
                    'mapped' => false,
                ));
            }
            if (null === $fieldEntity || $fieldEntity->getEnabled()) {
                $form->add('disabledHousing', 'text', array(
                    'data' => 'Something',
                    'attr' => ['style' => 'display:none;'],
                    'mapped' => false,
                ));
            }

            $housing = new DisabledOptions('Housing', 'housing', 'Housing: ', $enabled);
            $form->add('housing', EntityType::class, $housing->fieldArray());
        });
    }

    public function getName()
    {
        return 'household';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'error_bubbling' => false,
            'cascade_validation' => true,
            'csrf_protection' => false,
            'required' => false,
            'attr' => array("class" => "smallform"),
            'label_attr' => ['style' => ['font-style' => 'bold']],
        ));
    }

}
