<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mana\ClientBundle\Form\MemberType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
                ->add('isHead', ChoiceType::class, array(
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $this->idArray,
                ))
                ->add('headId', HiddenType::class,array(
                    'mapped' => false,
                ))
                ->add('members', CollectionType::class, array(
                    'entry_type' => new MemberType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'error_bubbling' => false,
                    'prototype' => true,
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'csrf_protection' => false,
            'required' => false,
        ));
    }

}
