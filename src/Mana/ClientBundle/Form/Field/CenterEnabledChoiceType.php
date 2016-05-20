<?php

namespace Mana\ClientBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CenterEnabledChoiceType extends AbstractType
{

    public function getParent() {
        return 'entity';
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'class' => 'ManaClientBundle:Center',
            'choice_label' => 'center',
            'placeholder' => 'Select site',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('c')
                                ->where('c.enabled=1')
                                ->orderBy('c.center', 'ASC')
                ;
            },
            'constraints' => array(new NotBlank(array('message' => 'No site elected', 'groups' => array('Contact'))))
        ));
    }

}
