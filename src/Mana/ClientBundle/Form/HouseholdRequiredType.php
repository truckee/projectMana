<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mana\ClientBundle\Form\Field\CenterEnabledChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class HouseholdRequiredType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('center', CenterEnabledChoiceType::class, array(
                    'label' => 'Site ',
                ))
                ->add('compliance', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('complianceDate', DateType::class, array(
                    'attr' => [
                        'placeholder' => 'mm/dd/yyyy',
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
                ->add('shared', ChoiceType::class, array(
                    'choices' => array('0' => 'No', '1' => 'Yes'),
                ))
                ->add('sharedDate', DateType::class, array(
                    'attr' => [
                        'placeholder' => 'mm/dd/yyyy',
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Household',
            'required' => false,
        ));
    }

}
