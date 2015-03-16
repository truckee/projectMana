<?php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CenterEnabledChoiceType extends AbstractType
{
    public function getParent() {
        return 'entity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'empty_value' => 'Select site',
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.enabled=1')
                                ->orderBy('c.center', 'ASC')
                                ;
                    },
                    'constraints' => array(new NotBlank(array('message' => 'No site elected', 'groups' => array('Default'))))
            ));
    }

    public function getName()
    {
        return 'center';
    }
}
