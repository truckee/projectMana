<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

/**
 * Description of AddressType.
 *
 * @author George
 */
class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('addresstype', EntityType::class, array(
                    'class' => 'ManaClientBundle:AddressType',
                    'label' => 'Address type: ',
                    'choice_label' => 'addresstype',
                    'attr' => array('class' => 'smallform'),
                    'expanded' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                        ->orderBy('a.id', 'ASC')
                        ->where('a.enabled=1');
            },
                ))
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
                    'class' => 'ManaClientBundle:County',
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
                    'class' => 'ManaClientBundle:State',
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
            'data_class' => 'Mana\ClientBundle\Entity\Address',
        ));
    }
}
