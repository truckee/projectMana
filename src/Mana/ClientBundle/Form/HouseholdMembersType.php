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
    private $service;

    public function __construct($service, $idArray = null) {
        $this->idArray = $idArray;
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('isHead', 'choice', array(
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $this->idArray,
                ))
                ->add('headId', 'hidden', array(
                    'mapped' => false,
                ))
                ->add('members', 'collection', array(
                    'type' => new MemberType(),
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'by_reference' => false,
//                    'error_bubbling' => false,
//                    'cascade_validation' => true,
//                    'prototype' => true,
                ))
        ;
        // if a head of household is necessarily being replaced, copy
        // required data from member to head of household
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $newData = $this->service->replaceHeadData($data);
            $event->setData($newData);
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
//            'error_bubbling' => false,
//            'cascade_validation' => true,
            'csrf_protection' => false,
            'required' => false,
//            'attr' => array("class" => "smallform"),
        ));
    }

}
