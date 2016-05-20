<?php

// src\Mana\ClientBundle\Form\MemberCompleteType.php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Mana\ClientBundle\Form\MemberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MemberCompleteType extends MemberType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('include', ChoiceType::class, array(
                    'choices' => array('Yes' => 1, 'No' => 0),
                    'choices_as_values' => true,
                    'label' => 'Include? ',
                    'required' => false,
                ))
                ->add('excludeDate', DateType::class, array(
                    'required' => false,
                ))
                ->add('offences', EntityType::class, array(
                    'class' => 'ManaClientBundle:Offence',
                    'label' => 'Offense:',
                    'choice_label' => 'offence',
                    'label' => 'Offences: ',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.offence', 'ASC')
                                ->where('e.enabled=1');
                    },
                ))
                ->add('relation', EntityType::class, array(
                    'label' => 'Relationship:',
                    'class' => 'ManaClientBundle:Relationship',
                    'choice_label' => 'relation',
                    'expanded' => false,
                    'required' => false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                                ->orderBy('r.relation', 'ASC')
                                ->where('r.enabled=1');
                    },
                ))
                ->add('id', HiddenType::class,array(
                    'mapped' => false,
                ))
                ->add('work', EntityType::class, array(
                    'class' => 'ManaClientBundle:Work',
                    'choice_label' => 'work',
                    'label' => 'Work:',
                    'required' => false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('w')
                                ->orderBy('w.work', 'ASC')
                                ->where("w.enabled=1");
                    },
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Member',
        ));
    }

}
