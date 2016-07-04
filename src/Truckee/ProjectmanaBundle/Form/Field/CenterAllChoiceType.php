<?php

namespace Truckee\ProjectmanaBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CenterAllChoiceType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                    'class' => 'TruckeeProjectmanaBundle:Center',
                    'choice_label' => 'center',
                    'placeholder' => 'Select site',
                    'attr' => array('class' => 'smallform'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC')
                                ;
                    },
                    'constraints' => array(new NotBlank(array('message' => 'Site must be selected'))),
            ));
    }
}
