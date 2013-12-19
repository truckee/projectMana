<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatusType extends AbstractType {
    
    protected $years;

    public function __construct($years) {
        $this->years = $years;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('status', 'choice', array(
                'choices' => array('Yes' => 'Yes', 'No' => 'No'),
                'label' => 'Change status to: ',
                'attr' => array("class" => "smallform"),
                ))
            ->add('years', 'choice', array(
                'choices' => $this->years,
                'attr' => array("class" => "smallform"),
                ))
        ;
    }
    
    public function getName() {
        return 'status';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'required' => false,
            
        ));
    }


}
?>
