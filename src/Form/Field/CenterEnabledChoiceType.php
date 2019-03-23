<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CenterEnabledChoiceType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'App:Center',
            'label' => 'Site',
            'choice_label' => 'center',
            'placeholder' => 'Select site',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                                ->where('c.enabled=1')
                                ->orderBy('c.center', 'ASC')
                ;
            },
            'constraints' => array(new NotBlank(array('message' => 'No site elected', 'groups' => array('Contact')))),
        ));
    }
}
