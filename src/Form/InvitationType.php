<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Invitation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of NewUserType
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class InvitationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fname', TextType::class, [
                    'label' => 'First name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    ])
                ->add('sname', TextType::class, [
                    'label' => 'Last name: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('username', TextType::class, [
                    'label' => 'Username: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Email: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('token', HiddenType::class, [
                    'data' => md5(uniqid(rand(), true)),
                ])
                ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Invitation::class,
            'required' => false,
        ));
    }

}
