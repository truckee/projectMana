<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                    'center',
                    EntityType::class,
                    array(
                            'class' => 'App:Center',
                            'label' => 'Site:',
                            'choice_label' => 'center',
                            'placeholder' => 'Select site',
                            'attr' => (in_array('Center', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Center', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.center', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.center', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'contactdesc',
                    EntityType::class,
                    array(
                            'class' => 'App:Contactdesc',
                            'label' => 'Contact type',
                            'choice_label' => 'contactdesc',
                            'placeholder' => 'Select contact type',
                            'attr' => (in_array('Contactdesc', $options['disabledOptions']) ? ['disabled' => 'disabled'] : []),
                            'query_builder' => function (EntityRepository $er) use ($options) {
                                if (false === in_array('Contactdesc', $options['disabledOptions'])) {
                                    return $er->createQueryBuilder('alias')
                                            ->where('alias.enabled = 1')
                                            ->orderBy('alias.contactdesc', 'ASC');
                                } else {
                                    return $er->createQueryBuilder('alias')
                                            ->orderBy('alias.contactdesc', 'ASC');
                                }
                            }
                        )
                )
                ->add(
                    'contactDate',
                    DateType::class,
                    array(
                            'widget' => 'single_text',
                            'html5' => false,
                            'attr' => ['class' => 'js-datepicker'],
                            'format' => 'M/d/y',
                            'years' => range(date('Y'), date('Y') - 5),
                        )
                )
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
            'data_class' => 'App\Entity\Contact',
            'required' => false,
            'disabledOptions' => [],
        ));
    }
}
