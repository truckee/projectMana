<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SpecialneedType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('need', null, array(
        'constraints' => array(new NotBlank(array('message' => 'Need may not be blank')), ),
        ))
        ->add('enabled', 'choice', array(
        'choices' => array(
        1 => 'Yes',
        0 => 'No')
        ))
        ->add('order', 'choice', array(
        'constraints' => array(new NotBlank(array('message' => 'Order may not be blank')), ),
        'choices' => $this->choices(),
        ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Specialneed',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'specialneed';
    }

    private function choices() {
        $choice = array();
        for ($i = 1; $i <= 20; $i++) {
            $choice[$i] = $i;
        }
        return $choice;
    }

}
