<?php

namespace Mana\ClientBundle\Form;

use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Form\Field\DobAgeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseholdHeadType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fname', TextType::class, array(
                    'label' => 'First name ',
                ))
                ->add('sname', TextType::class, array(
                    'label' => 'Last name ',
                ))
                ->add('dob', DobAgeType::class, array(
                    'label' => 'DOB or age ',
                ))
                ->add('sex', ChoiceType::class, array(
                    'label' => 'Gender ',
                    'placeholder' => 'Select gender',
                    'choices' => array('Male' => 'Male', 'Female' => 'Female'),
                    'choices_as_values' => true,
                ))
                ->add('ethnicity', EntityType::class, array(
                    'label' => 'Ethnicity',
                    'class' => 'ManaClientBundle:Ethnicity',
                    'choice_label' => 'ethnicity',
                    'expanded' => false,
                    'placeholder' => 'Select ethnicity',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->orderBy('e.ethnicity', 'ASC')
                ;
            },
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Member',
            'required' => false,
        ));
    }

}
