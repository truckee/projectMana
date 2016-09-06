<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Truckee\ProjectmanaBundle\Form\Field\CenterEnabledChoiceType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('center', CenterEnabledChoiceType::class)
                ->add('contactDesc', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:ContactDesc',
                    'label' => 'Contact type',
                    'choice_label' => 'contactDesc',
                    'placeholder' => 'Select contact type',
                    'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                        ->where('c.enabled=1')
                        ->orderBy('c.contactDesc', 'ASC')
                ;
            },
                ))
                ->add('contactDate', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                    'years' => range(date('Y'), date('Y') - 5),
                ))
                ->add('household', CheckboxType::class, array(
                    'mapped' => false,
                ))
                ->add('householdId', TextType::class, array(
                    'mapped' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Contact',
            'required' => false,
        ));
    }
}