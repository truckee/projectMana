<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Form\MailingAddressType.php

namespace Truckee\ProjectmanaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Truckee\ProjectmanaBundle\Form\Field\YesNoRadioType;
use Truckee\ProjectmanaBundle\Form\AddressDataType;

/**
 * PhysicalAddressType
 *
 */
class MailingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mailing', YesNoRadioType::class, [
                'mapped' => false,
                'label' => 'Mailing address: ',
                'attr' => ['class' => 'form-inline'],
            ])
            ->add('address', AddressDataType::class)
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Truckee\ProjectmanaBundle\Entity\Address',
        ));
    }
}
