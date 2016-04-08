<?php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CenterAllChoiceType extends AbstractType
{
    public function getParent() {
        return 'entity';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'empty_value' => 'Select site',
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC')
                                ;
                    },
                    'constraints' => array(new NotBlank(array('message' => 'Site must be selected')),),
            ));
    }

    public function getName()
    {
        return 'center';
    }
}
