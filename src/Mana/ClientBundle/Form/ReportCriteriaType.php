<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mana\ClientBundle\Form\Field\MonthType;
use Mana\ClientBundle\Form\Field\YearType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Mana\ClientBundle\Validator\Constraints\CenterOrCounty;
use Mana\ClientBundle\Validator\Constraints\StartEndDate;

class ReportCriteriaType extends AbstractType {

    private $month;
    private $year;

    public function __construct() {
        $date = strtotime(date('Y-m') . " -1 month");
        $this->month = date('n', $date);
        $this->year = date('Y', $date);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('startMonth', \Mana\ClientBundle\Form\Field\MonthType::class, array(
                    'data' => $this->month,
                    'placeholder' => false,
                    'constraints' => array(
                        new StartEndDate(),
                    ),
                ))
                ->add('endMonth', \Mana\ClientBundle\Form\Field\MonthType::class, array(
                    'data' => $this->month,
                    'placeholder' => false,
                        )
                )
                ->add('startYear', \Mana\ClientBundle\Form\Field\YearType::class, array(
                    'data' => $this->year,
                    'placeholder' => false,
                        )
                )
                ->add('endYear', \Mana\ClientBundle\Form\Field\YearType::class, array(
                    'data' => $this->year,
                    'placeholder' => false,
                        )
                )
                ->add('contact_type', EntityType::class, array(
                    'class' => 'ManaClientBundle:ContactDesc',
                    'choice_label' => 'contactDesc',
                    'placeholder' => 'Select contact type',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.enabled = 1')
                                ->orderBy('c.contactDesc', 'ASC');
                    },
                    'constraints' => array(
                        new CenterOrCounty(),
                    ),
                ))
                ->add('county', EntityType::class, array(
                    'class' => 'ManaClientBundle:County',
                    'choice_label' => 'county',
                    'placeholder' => 'Select county',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC');
                    },
                ))
                ->add('center', new Field\CenterEnabledChoiceType())
                ->add('dest', 'hidden')
                ->add('columnType', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                    'mapped' => FALSE,
                    'choices' => ['center' => 'By site', 'county' => 'By county'],
                    'expanded' => TRUE,
                    'data' => 'center',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'required' => false,
            'attr' => array("class" => "smallform"),
            'validation_groups' => array('reports'),
        ));
    }

}
