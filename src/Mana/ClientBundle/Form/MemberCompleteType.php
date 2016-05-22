<?php

// src\Mana\ClientBundle\Form\MemberCompleteType.php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Mana\ClientBundle\Form\MemberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MemberCompleteType extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return MemberType::class;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefined(array('image_path'));
    }

}
