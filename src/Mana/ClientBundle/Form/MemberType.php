<?php

// src\Mana\ClientBundle\Form\MemberType.php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;
//use Mana\ClientBundle\Entity\Member;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MemberType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('include', 'choice', array(
                    'choices' => array(1 => 'Yes', 0 => 'No'),
                    'empty_value' => false,
                    'label' => 'Include? ',
                    'label_attr' => $options['label_attr'],
                    'attr' => array("class" => "smallform"),
//                    'data' => 1,
                ))
                ->add('fname', TextType::class, array(
                    'label' => 'First name:',
                    'label_attr' => $options['label_attr'],
                    'attr' => array(
                        'size' => 15,
                        'maxlength' => 45,
                        'class' => 'smallform')
                ))
                ->add('sname', 'text', array(
                    'label' => 'Last name:',
                    'label_attr' => $options['label_attr'],
                    'attr' => array(
                        "class" => "smallform",
                        'size' => 15,
                        'maxlength' => 45,)
                ))
                ->add('dob', 'dob_age', array(
                    'label' => 'DOB or age:',
                    'label_attr' => $options['label_attr'],
                    'attr' => array(
                        "class" => "smallform",
                        'size' => 15,)
                ))
                ->add('sex', 'choice', array(
                    'label' => 'Gender:',
                    'label_attr' => $options['label_attr'],
                    'choices' => array('Male' => 'Male', 'Female' => 'Female'),
                    'empty_value' => '',
                    'attr' => array("class" => "smallform"),
                ))
                ->add('excludeDate', 'dob_age')
                ->add('ethnicity', 'entity', array(
                    'label' => 'Ethnicity:',
                    'label_attr' => $options['label_attr'],
                    'class' => 'ManaClientBundle:Ethnicity',
                    'property' => 'abbreviation',
                    'attr' => array("class" => "smallform"),
                    'expanded' => false,
                    'empty_value' => '',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->orderBy('e.abbreviation', 'ASC')
//                            ->where('e.enabled=1')
                ;
            },
                ))
                ->add('offences', 'entity', array(
                    'class' => 'ManaClientBundle:Offence',
                    'label' => 'Offense:',
                    'label_attr' => $options['label_attr'],
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
                    'label' => 'Relationship:',
                    'class' => 'ManaClientBundle:Relationship',
                    'property' => 'relation',
                    'label_attr' => $options['label_attr'],
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
                    'label' => 'Work:',
                    'label_attr' => $options['label_attr'],
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
//                ->add('insurance', 'choice,', array(
//                    'choices' => array('0' => 'No', '1' => 'Yes', '2' => 'Appl.'),
//                    'empty_value' => '',
//                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Member',
//            'cascade_validation' => false,
//            'error_bubbling' => false,
//            'csrf_protection' => false,
//            'required' => false,
            'label_attr' => ['style' => 'font-weight: bold;'],
//            'attr' => array("class" => "smallform"),
//            'empty_data' => function (FormInterface $form) {
//        return new Member($form->get('fname')->getData());
//    },
//            'validation_groups' => function(FormInterface $form) {
//        $data = $form->getData();
//        //household is null for new head of household only
//        $testValue = $data->getHousehold()->getId();
//        if (empty($testValue)) {
//            return array('sname', 'Default');
//        }
//    },
        ));
    }

    public function getName()
    {
        return 'member';
    }

}
