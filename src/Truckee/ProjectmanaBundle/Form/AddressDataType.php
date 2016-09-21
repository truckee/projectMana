<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Form\AddressDataType.php

namespace Truckee\ProjectmanaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

/**
 * Description of AddressType.
 *
 * @author George Brooks
 */
class AddressDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('line1', TextType::class, array(
                    'label' => 'Address, line 1: ',
                    'required' => false,
                ))
                ->add('line2', TextType::class, array(
                    'label' => 'Address, line 2: ',
                    'required' => false,
                ))
                ->add('city', TextType::class, array(
                    'label' => 'City: ',
                    'required' => false,
                ))
                ->add('county', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:County',
                    'label' => 'County: ',
                    'choice_label' => 'county',
                    'expanded' => false,
                    'placeholder' => '',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC')
                                ->where('c.enabled=1');
                    },
                ))
                ->add('state', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:State',
                    'label' => 'State: ',
                    'choice_label' => 'state',
                    'expanded' => false,
                    'placeholder' => '',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                                ->orderBy('s.state', 'ASC')
                                ->where('s.enabled=1');
                    },
                ))
                ->add('zip', TextType::class, array(
                    'label' => 'Zip: ',
                    'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true
        ));
    }
}
