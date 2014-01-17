<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('isActive', 'choice', array(
                    'choices' => array(1 => 'Yes', 0 => 'No'),
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Enabled: ',
                ))
                ->add('fname')
                ->add('sname')
                ->add('email')
                ->add('role', 'choice', array(
                    'choices' => array('ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin'),
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Group: ',
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\User',
            'csrf_protection' => false,
            'required' => false,
            'attr' => array('class' => 'smallform'),
            'validation_groups' => array('edit'),
        ));
    }

    public function getName() {
        return 'user';
    }

}

?>
