<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('center', new Field\CenterEnabledChoiceType())
                ->add('contactDesc', EntityType::class, array(
                    'class' => 'ManaClientBundle:ContactDesc',
                    'label' => 'Contact type',
                    'choice_label' => 'contactDesc',
                    'placeholder' => 'Select contact type',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('c')
                        ->where('c.enabled=1')
                        ->orderBy('c.contactDesc', 'ASC')
                ;
            },
                ))
                ->add('contactDate', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                    'format' => 'M/d/y',
                    'years' => range(date('Y'), date('Y') - 5),
                ))
                ->add('household', ChoiceType::class, array(
                    'mapped' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->add('householdId', TextType::class, array(
                    'mapped' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Contact',
            'csrf_protection' => false,
            'required' => false,
        ));
    }

}
