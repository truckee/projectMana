<?php

namespace Mana\ClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mana\ClientBundle\Form\Field\MonthType;
use Mana\ClientBundle\Form\Field\YearType;
use Doctrine\ORM\EntityRepository;
use Mana\ClientBundle\Validator\Constraints\CenterOrCounty;
use Mana\ClientBundle\Validator\Constraints\StartEndDate;
//use Mana\ClientBundle\Validator\Constraints\EmptyTable;

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
                ->add('startMonth', new MonthType(), array(
                    'data' => $this->month,
                    'empty_value' => false,
                    'attr' => array("class" => "smallform"),
                    'constraints' => array(
                        new StartEndDate(),
                    ),
                ))
                ->add('endMonth', new MonthType(), array(
                    'data' => $this->month,
                    'empty_value' => false,
                    'attr' => array("class" => "smallform"),
//                    'constraints' => array(
//                        new EmptyTable(),
//                    ),
                        )
                )
                ->add('startYear', new YearType(), array(
                    'data' => $this->year,
                    'empty_value' => false,
                    'attr' => array("class" => "smallform"),
                        )
                )
                ->add('endYear', new YearType(), array(
                    'data' => $this->year,
                    'empty_value' => false,
                    'attr' => array("class" => "smallform"),
                        )
                )
                ->add('contact_type_id', 'entity', array(
                    'class' => 'ManaClientBundle:ContactDesc',
                    'property' => 'contactDesc',
                    'empty_value' => 'Select contact type',
                    'error_bubbling' => true,
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.contactDesc', 'ASC');
                    },
                    'constraints' => array(
                        new CenterOrCounty(),
                    ),
                ))
                ->add('county_id', 'entity', array(
                    'class' => 'ManaClientBundle:County',
                    'property' => 'county',
                    'empty_value' => 'Select county',
                    'error_bubbling' => true,
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC');
                    },
                ))
                ->add('center_id', 'entity', array(
                    'class' => 'ManaClientBundle:Center',
                    'property' => 'center',
                    'empty_value' => 'Select distribution center',
                    'error_bubbling' => true,
                    'attr' => array("class" => "smallform"),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.center', 'ASC');
                    },
                ))
                ->add('dest', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'required' => false,
            'attr' => array("class" => "smallform"),
        ));
    }

    public function getName() {
        return 'report_criteria';
    }

}

?>