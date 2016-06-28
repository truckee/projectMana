<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StatusType extends AbstractType
{
    protected $years;

    public function __construct($years)
    {
        $this->years = $years;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, array(
                'choices' => array('Yes' => 'Yes', 'No' => 'No'),
                'choices_as_values' => true,
                'label' => 'Change status to: ',
                ))
            ->add('years', ChoiceType::class, array(
                'choices' => $this->years,
                'choices_as_values' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,

        ));
    }
}
