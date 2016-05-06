<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType {

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
                ->add('email', EmailType::class)
                ->add('username')
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Password fields do not match',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ))
                ->add('role', 'choice', array(
                    'choices' => array('ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin'),
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Group: ',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\User',
            'csrf_protection' => false,
            'required' => false,
            'attr' => array('class' => 'smallform'),
        ));
    }

    public function getName() {
        return 'user';
    }

}

?>
