<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of AddressType
 *
 * @author George
 */
class AddressType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('addresstype', 'entity', array(
                    'class' => 'ManaClientBundle:AddressType',
                    'property' => 'addresstype',
                    'attr' => array("class" => "smallform"),
                    'expanded' => false,
                    'empty_value' => '',
                    'error_bubbling'    => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                                ->orderBy('a.id', 'ASC')
                            ->where("a.enabled=1");
                    },
                ))
                ->add('line1', 'text', array(
                    'attr' => array(
                        'size' => 20,
                        "class" => "smallform"
                    )
                ))
                ->add('line2', 'text', array(
                    'attr' => array(
                        'size' => 20,
                        "class" => "smallform"
                    )
                ))
                ->add('city')
                ->add('county', 'entity', array(
                    'class' => 'ManaClientBundle:County',
                    'property' => 'county',
                    'expanded' => false,
                    'empty_value' => 'County',
                    'attr' => array("class" => "smallform"),
                    'error_bubbling'    => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC')
                            ->where("c.enabled=1");
                    },
                ))
                ->add('state', 'entity', array(
                    'class' => 'ManaClientBundle:State',
                    'property' => 'state',
                    'expanded' => false,
                    'empty_value' => 'State',
                    'attr' => array("class" => "smallform"),
                    'error_bubbling'    => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                                ->orderBy('s.state', 'ASC')
                            ->where("s.enabled=1");
                    },
                ))
                ->add('zip', 'text', array(
                    'attr' => array(
                        'size' => 5,
                        "class" => "smallform"
                    )
                ))
                ;
    }

    public function getName() {
        return 'address';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Mana\ClientBundle\Entity\Address',
            'cascade_validation' => true,
            'csrf_protection' => false,
            'required' => false,
            
        ));
    }
}

?>
