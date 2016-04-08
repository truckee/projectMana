<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Mana\ClientBundle\EventListener\UserPasswordSubscriber;

class UserPasswordType extends AbstractType {
//        $subscriber = new UserPasswordSubscriber($builder->getFormFactory());
//        $builder->addEventSubscriber($subscriber);

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Password fields do not match',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
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
