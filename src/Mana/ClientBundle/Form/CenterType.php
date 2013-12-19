<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CenterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('center', null, array(
                'constraints' => array(new NotBlank(array('message' => 'Center may not be blank')),),
            ))
            ->add('enabled', 'choice', array(
                    'choices' => array(
                1 => 'Yes',
                0 => 'No')
            ))
                ->add('county', 'entity', array(
                    'constraints' => array(new NotBlank(array('message' => 'County may not be blank')),),
                    'class' => 'ManaClientBundle:County',
                    'property' => 'county',
                    'expanded' => false,
                    'empty_value' => 'County',
                    'error_bubbling'    => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC');
                    },
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Center',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'center';
    }
}
