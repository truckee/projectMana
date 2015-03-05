<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mana\ClientBundle\Form\MemberType;
//use Mana\ClientBundle\Form\AddressType;
//use Mana\ClientBundle\Form\PhoneType;
//use Symfony\Component\Form\FormEvents;
//use Symfony\Component\Form\FormEvent;
//use Doctrine\ORM\EntityRepository;

class HouseholdHeadType extends AbstractType {

//    private $idArray;
//
//    public function __construct($idArray = null) {
//        $this->idArray = $idArray;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('members', 'collection', array(
                    'type' => new MemberType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'error_bubbling' => false,
                    'cascade_validation' => true,
                    'prototype' => true,
                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
//            $data = $event->getData();
//            if ($data['headId'] <> 0) {
//                $formerHeadId = $data['headId'];  //original hoh_id
//                $newHeadId = $data['isHead'];  //new hoh_id
//                if ($newHeadId <> $formerHeadId) {
//                    //get the updated values
//                    $v1 = false;
//                    foreach ($data['members'] as $member) {
//                        if (array_key_exists('id', $member) && $member['id'] == $newHeadId) {
//                            $dob = $member['dob'];
//                            $sex = $member['sex'];
//                            $ethnicity = $member['ethnicity'];
//                        } elseif ($member['id'] == $formerHeadId && !array_key_exists('dob', $member)) {
//                            $v1 = true;
//                        }
//                    }
//                    for ($index = 0; $index < count($data['members']); $index++) {
//                        //set the new values
//                        if (array_key_exists('id', $data['members'][$index]) && $data['members'][$index]['id'] == $formerHeadId && $v1 == true) {
//                            $data['members'][$index]['dob'] = $dob;
//                            $data['members'][$index]['sex'] = $sex;
//                            $data['members'][$index]['ethnicity'] = $ethnicity;
//                        }
//                    }
//                }
//                $event->setData($data);
//            }
//        });
    }

    public function getName() {
        return 'household_head';
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