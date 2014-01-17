<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class NotfoodstampType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notfoodstamp', null, array(
                'constraints' => array(new NotBlank(array('message' => 'Reason may not be blank')),),
            ))
            ->add('enabled', 'choice', array(
                    'choices' => array(
                1 => 'Yes',
                0 => 'No')
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Notfoodstamp',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'notfoodstamp';
    }
}
