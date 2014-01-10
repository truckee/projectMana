<?php

// src\Mana\ClientBundle\Form\MemberType.php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;
use Mana\ClientBundle\Entity\Member;

class MemberType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('include', 'choice', array(
                    'choices' => array(1 => 'Yes', 0 => 'No'),
                    'empty_value' => false,
                    'attr' => array("class" => "smallform"),
                ))
                ->add('fname', 'text', array(
                    'attr' => array(
                        'size' => 15,
                        'maxlength' => 45,
                        'class' => 'smallform')
                ))
                ->add('sname', 'text', array(
                    'attr' => array(
                        "class" => "smallform",
                        'size' => 15,
                        'maxlength' => 45,)
                ))
                ->add('dob', 'dob_age', array(
                    'attr' => array(
                        "class" => "smallform",
                        'size' => 15,)
                ))
                ->add('sex', 'choice', array(
                    'choices' => array('Male' => 'Male', 'Female' => 'Female'),
                    'empty_value' => '',
                    'attr' => array("class" => "smallform"),
                ))
                ->add('excludeDate', 'dob_age')
                ->add('ethnicity', 'entity', array(
                    'class' => 'ManaClientBundle:Ethnicity',
                    'property' => 'abbreviation',
                    'attr' => array("class" => "smallform"),
                    'expanded' => false,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->orderBy('e.abbreviation', 'ASC');
            },
                ))
                ->add('offences', 'entity', array(
                    'class' => 'ManaClientBundle:Offence',
                    'property' => 'offence',
                    'attr' => array("class" => "smallform"),
                    'label' => 'Offences: ',
                    'expanded' => true,
                    'multiple' => true,
                    'empty_value' => false,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->orderBy('e.offence', 'ASC')
                            ->where('e.enabled=1');
            },
                ))
                ->add('relation', 'entity', array(
//                    'attr' => array('size' => 15,),
                    'class' => 'ManaClientBundle:Relationship',
                    'property' => 'relation',
                    'attr' => array("class" => "smallform"),
                    'expanded' => false,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('r')
                        ->orderBy('r.relation', 'ASC')
                            ->where('r.enabled=1');
            },
                ))
                ->add('id', 'hidden', array(
                    'mapped' => false,
                ))
                ->add('work', 'entity', array(
                    'class' => 'ManaClientBundle:Work',
                    'property' => 'work',
                    'attr' => array("class" => "smallform"),
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('w')
                                ->orderBy('w.work', 'ASC')
                            ->where("w.enabled=1");
                    },
                ))
//                ->add('shots', 'choice', array(
//                    'choices' => array('0' => 'No', '1' => 'Yes'),
//                    'empty_value' => '',
//                ))
//                ->add('insurance', 'choice', array(
//                    'choices' => array('0' => 'No', '1' => 'Yes', '2' => 'Appl.'),
//                    'empty_value' => '',
//                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Member',
            'cascade_validation' => false,
            'error_bubbling' => false,
            'csrf_protection' => false,
            'required' => false,
//            'attr' => array("class" => "smallform"),
            'empty_data' => function (FormInterface $form) {
                return new Member($form->get('fname')->getData());
            },
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                //household is null for new head of household only
                $testValue = $data->getHousehold()->getId();
                if (empty($testValue)) {
                    return array('sname', 'Default');
                }
            },
        ));
    }

    public function getName() {
        return 'member';
    }

}
