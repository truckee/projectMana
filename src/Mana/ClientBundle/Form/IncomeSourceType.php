<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class IncomeSourceType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('incomeSource', null, array(
                'constraints' => array(new NotBlank(array('message' => 'Source may not be blank')),),
            ))
            ->add('enabled', 'choice', array(
                    'choices' => array(
                1 => 'Yes',
                0 => 'No')
            ))
//            ->add('incomeAbbreviation', null, array(
//                'constraints' => array(new NotBlank(array('message' => 'Abbreviationeviation may not be blank')),),
//            ))
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\IncomeSource',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'incomesource';
    }
}
