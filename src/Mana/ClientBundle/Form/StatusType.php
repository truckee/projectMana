<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StatusType extends AbstractType {
    
    protected $years;

    public function __construct($years) {
        $this->years = $years;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('status', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                'choices' => array('Yes' => 'Yes', 'No' => 'No'),
                'label' => 'Change status to: ',
                'attr' => array("class" => "smallform"),
                ))
            ->add('years', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                'choices' => $this->years,
                'attr' => array("class" => "smallform"),
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'required' => false,
            
        ));
    }


}
?>
