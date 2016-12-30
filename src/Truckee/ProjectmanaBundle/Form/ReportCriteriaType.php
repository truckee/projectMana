<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Truckee\ProjectmanaBundle\Form\Field\CenterEnabledChoiceType;
use Truckee\ProjectmanaBundle\Form\Field\MonthType;
use Truckee\ProjectmanaBundle\Form\Field\YearType;
use Truckee\ProjectmanaBundle\Validator\Constraints\CenterOrCounty;
use Truckee\ProjectmanaBundle\Validator\Constraints\StartEndDate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportCriteriaType extends AbstractType
{
    private $month;
    private $year;

    public function __construct()
    {
        $date = strtotime(date('Y-m').' -1 month');
        $this->month = date('n', $date);
        $this->year = date('Y', $date);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('startMonth', MonthType::class, array(
                    'data' => $this->month,
                    'placeholder' => false,
                    'constraints' => array(
                        new StartEndDate(),
                    ),
                ))
                ->add('endMonth', MonthType::class, array(
                    'data' => $this->month,
                    'placeholder' => false,
                        )
                )
                ->add('startYear', YearType::class, array(
                    'data' => $this->year,
                    'placeholder' => false,
                        )
                )
                ->add('endYear', YearType::class, array(
                    'data' => $this->year,
                    'placeholder' => false,
                    'constraints' => [
                        new StartEndDate(),
                    ],
                        )
                )
                ->add('contact_type', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:ContactDesc',
                    'choice_label' => 'contactDesc',
                    'placeholder' => 'Select contact type',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.enabled = 1')
                                ->orderBy('c.contactDesc', 'ASC');
                    },
                    'constraints' => array(
                        new CenterOrCounty(),
                    ),
                ))
                ->add('county', EntityType::class, array(
                    'class' => 'TruckeeProjectmanaBundle:County',
                    'choice_label' => 'county',
                    'placeholder' => 'Select county',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.county', 'ASC');
                    },
                ))
                ->add('center', CenterEnabledChoiceType::class)
                ->add('dest', HiddenType::class)
                ->add('columnType', ChoiceType::class, [
                    'mapped' => false,
                    'choices' => ['By site' => 'center', 'By county' => 'county'],
                    'expanded' => true,
                    'data' => 'center',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }
}
