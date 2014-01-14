<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contactDesc', 'entity', array(
                    'class' => 'ManaClientBundle:ContactDesc',
                    'property' => 'contactDesc',
                    'empty_value' => 'Select contact type',
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.contactDesc', 'ASC')
                            ->where('c.enabled=1');
                    },
                ))
            ->add('contactDate', 'date', array(
                'format' => 'M/d/y',
                'label' => '<b>Date:</b> ',
                'years' => range(date('Y'), date('Y') - 5),
            ))
            ->add('center', 'entity', array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'empty_value' => 'Select distribution site',
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC')
                            ->where('c.enabled=1');
                    },
                ))
            ->add('household', 'choice', array(
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('householdId','text',array(
                'mapped' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Contact',
            'csrf_protection' => false,
            'required' => false,
            ));
    }

    public function getName()
    {
        return 'contact';
    }
}
