<?php

// src\Mana\ClientBundle\Form\MemberType.php

namespace Mana\ClientBundle\Form;

use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Form\Field\DobAgeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fname', TextType::class, array(
                    'label' => 'First name:',
                    'label_attr' => [
                        'required' => 'required',
                    ],
                ))
                ->add('sname', TextType::class, array(
                    'label' => 'Last name:',
                ))
                ->add('dob', DobAgeType::class, array(
                    'label' => 'DOB or age:',
                ))
                ->add('sex', ChoiceType::class, array(
                    'label' => 'Gender:',
                    'placeholder' => 'Select gender',
                    'choices' => array('Male' => 'Male', 'Female' => 'Female'),
                ))
                ->add('ethnicity', EntityType::class, array(
                    'label' => 'Ethnicity:',
                    'class' => 'ManaClientBundle:Ethnicity',
                    'choice_label' => 'abbreviation',
                    'expanded' => false,
                    'placeholder' => 'Select ethnicity',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.abbreviation', 'ASC')
                        ;
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
