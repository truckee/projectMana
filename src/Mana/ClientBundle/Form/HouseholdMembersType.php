<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mana\ClientBundle\Form\MemberType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class HouseholdMembersType extends AbstractType
{

    private $idArray;

    public function __construct($idArray)
    {
        $this->idArray = $idArray;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                ->add('headId', 'hidden', array(
                    'mapped' => false,
                ))
                ->add('isHead', 'choice', array(
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $this->idArray,
                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $formerHeadId = $data['headId'];  //original hoh_id
            $newHeadId = $data['isHead'];  //new hoh_id
            if ($newHeadId <> $formerHeadId) {
                //get the updated values
                foreach ($data['members'] as $member) {
                    if ($member['id'] == $newHeadId) {
                        $dob = $member['dob'];
                        $sex = $member['sex'];
                        $ethnicity = $member['ethnicity'];
                    }
                }

                for ($index = 0; $index < count($data['members']); $index++) {
                    //set the new values
                    if ($data['members'][$index]['id'] == $formerHeadId) {
                        $data['members'][$index]['dob'] = $dob;
                        $data['members'][$index]['sex'] = $sex;
                        $data['members'][$index]['ethnicity'] = $ethnicity;
                    }
                }
            }
            $event->setData($data);
            dump($data);
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
        ));
    }

}
