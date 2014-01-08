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

    public function __construct($idArray = null) {
        $this->idArray = $idArray;
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
//                    'data' => date('n'),
                ))
                ->add('arrivalyear', new Type\YearType(), array(
                    'empty_value' => false,
                ))
            ->add('center', 'entity', array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'constraints' => array(new NotBlank(array('message' => 'First site not selected')),),
                    'empty_value' => 'Select first site',
//                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC')
//                            ->where('c.enabled=1')
                                ;
                    },
                ))
                ->add('compliance', 'choice', array(
                    'choices' => array('1' => 'Yes', '0' => 'No'),
                    'data' => 1,
                ))
                ->add('complianceDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'data' => date_create()
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
//                                ->orderBy('i.income', 'ASC')
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
//                    'prototype' => true,
//                    'prototype_name' => '__address__',
                ))
//                ->add('pregnant', 'choice', array(
//                    'choices' => array('0' => 'No', '1' => 'Yes'),
//                    'empty_value' => '',
//                ))
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
                    'choices' => array('1' => 'Yes', '0' => 'No'),
                    'data' => 1,
                ))
                ->add('sharedDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'data' => date_create()
                ))
//                ->add('specialneed', 'entity', array(
//                    'class' => 'ManaClientBundle:Specialneed',
//                    'property' => 'need',
//                    'empty_value' => '',
//                    'query_builder' => function(EntityRepository $er) {
//                        return $er->createQueryBuilder('s')
//                                ->orderBy('s.order', 'ASC')
//                            ->where("s.enabled=1");
//                    },
//                ))
//                ->add('wic', 'choice', array(
//                    'choices' => array('0' => 'No', '1' => 'Yes', '2' => 'Appl.'),
//                    'empty_value' => '',
//                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if ($data['headId'] <> 0) {
                $formerHeadId = $data['headId'];  //original hoh_id
                $newHeadId = $data['isHead'];  //new hoh_id
//                        $flags = $data['flags'];
                if ($newHeadId <> $formerHeadId) {
                    //get the updated values
                    $v1 = false;
                    foreach ($data['members'] as $member) {
                        if (array_key_exists('id', $member) && $member['id'] == $newHeadId) {
                            $dob = $member['dob'];
                            $sex = $member['sex'];
                            $ethnicity = $member['ethnicity'];
                        } elseif ($member['id'] == $formerHeadId && !array_key_exists('dob', $member)) {
                            $v1 = true;
                        }
                    }
                    for ($index = 0; $index < count($data['members']); $index++) {
                        //set the new values
                        if (array_key_exists('id', $data['members'][$index]) && $data['members'][$index]['id'] == $formerHeadId && $v1 == true) {
                            $data['members'][$index]['dob'] = $dob;
                            $data['members'][$index]['sex'] = $sex;
                            $data['members'][$index]['ethnicity'] = $ethnicity;
                        }
                    }
                }
                $event->setData($data);
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
