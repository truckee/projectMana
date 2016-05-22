<?php

namespace Mana\ClientBundle\Form;

use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Form\AddressType;
use Mana\ClientBundle\Form\MemberType;
use Mana\ClientBundle\Form\PhoneType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Mana\ClientBundle\Utilities\DisabledOptions;

class HouseholdType extends AbstractType
{

    private $idArray;
//    private $newHeadService;
    private $new;

    public function __construct( $idArray = null, $new = null)
    {
        $this->idArray = $idArray;
//        $this->newHeadService = $newHeadService;
        $this->new = $new;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $new = $this->new;
        $builder
                ->add('active', ChoiceType::class, array(
                    'choices' => array('1' => 'Yes', '0' => 'No'),
                    'data' => 1,
                ))
                ->add('addresses', CollectionType::class, array(
                    'entry_type' => new AddressType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_name' => '__address__',
                ))
                ->add('appliances', EntityType::class, array(
                    'class' => 'ManaClientBundle:Appliance',
                    'choice_label' => 'appliance',
                    'label' => 'Appliances: ',
                    'expanded' => true,
                    'multiple' => true,
                    'placeholder' => false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                                ->orderBy('a.appliance', 'ASC')
                                ->where("a.enabled=1");
                    },
                ))
                ->add('arrivalmonth', new Field\MonthType(), array(
                    'placeholder' => false,
                ))
                ->add('arrivalyear', new Field\YearType(), array(
                    'placeholder' => false,
                ))
                ->add('compliance', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('complianceDate', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('dateAdded', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('foodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:FsStatus',
                    'choice_label' => 'status',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.id', 'ASC')
                        ;
                    },
                ))
                ->add('fsamount', EntityType::class, array(
                    'class' => 'ManaClientBundle:FsAmount',
                    'choice_label' => 'amount',
                    'label' => 'If yes, how much?',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.amount', 'ASC')
                                ->where("f.enabled=1");
                    },
                ))
                ->add('headId', HiddenType::class,array(
                    'mapped' => false,
                ))
                ->add('housing', EntityType::class, array(
                    'class' => 'ManaClientBundle:Housing',
                    'choice_label' => 'housing',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) use($new) {
                        if (true === $new) {
                            return $er->createQueryBuilder('h')
                                    ->orderBy('h.housing', 'ASC')
                                    ->where("h.enabled=1");
                        }
                        else {
                            return $er->createQueryBuilder('h')
                                    ->orderBy('h.housing', 'ASC');
                        }
                    },
                ))
                ->add('income', EntityType::class, array(
                    'class' => 'ManaClientBundle:Income',
                    'choice_label' => 'income',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->where("i.enabled=1");
                    },
                ))
                ->add('incomeSource', EntityType::class, array(
                    'class' => 'ManaClientBundle:IncomeSource',
                    'choice_label' => 'incomeSource',
                    'label' => 'Income sources: ',
                    'expanded' => true,
                    'multiple' => true,
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->orderBy('i.incomeSource', 'ASC')
                                ->where("i.enabled=1");
                    },
                ))
                ->add('isHead', ChoiceType::class, array(
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $this->idArray,
                ))
                ->add('notfoodstamp', EntityType::class, array(
                    'class' => 'ManaClientBundle:Notfoodstamp',
                    'label' => 'If no, why not? ',
                    'choice_label' => 'notfoodstamp',
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.notfoodstamp', 'ASC')
                                ->where("f.enabled=1");
                    },
                ))
                ->add('phones', CollectionType::class, array(
                    'entry_type' => new PhoneType(),
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
                    'placeholder' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                                ->orderBy('r.reason', 'ASC')
                                ->where("r.enabled=1");
                    },
                ))
                ->add('shared', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('sharedDate', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
//            $data = $event->getData();
//            if ($data['headId'] <> 0) {
//                $newData = $this->newHeadService->replaceHeadData($data);
//                $event->setData($newData);
//            }
//        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $household = $event->getData();
            $form = $event->getForm();
            $new = $this->new;
            $center = $household->getCenter();
            if (empty($center)) {
                $form->add('center', new Field\CenterEnabledChoiceType());
            }
            else {
                $form->add('center', new Field\CenterAllChoiceType());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'error_bubbling' => false,
            'csrf_protection' => false,
            'required' => false,
            'attr' => array("class" => "smallform"),
        ));
    }

}
