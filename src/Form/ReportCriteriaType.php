<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Field\CenterEnabledChoiceType;
use App\Form\Field\MonthType;
use App\Form\Field\YearType;
use App\Entity\Contactdesc;
use App\Validator\Constraints\CenterOrCounty;
use App\Validator\Constraints\StartEndDate;

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
                ->add(
                    'endMonth',
                    MonthType::class,
                    array(
                    'data' => $this->month,
                    'placeholder' => false,
                        )
                )
                ->add(
                    'startYear',
                    YearType::class,
                    array(
                    'data' => $this->year,
                    'placeholder' => false,
                        )
                )
                ->add(
                    'endYear',
                    YearType::class,
                    array(
                    'data' => $this->year,
                    'placeholder' => false,
                    'constraints' => [
                        new StartEndDate(),
                    ],
                        )
                )
                ->add('contactdesc', EntityType::class, array(
                    'class' => Contactdesc::class,
                    'choice_label' => 'contactdesc',
                    'placeholder' => 'Select contact type',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.enabled = 1')
                                ->orderBy('c.contactdesc', 'ASC');
                    },
                    'constraints' => array(
                        new CenterOrCounty(),
                    ),
                ))
                ->add('county', EntityType::class, array(
                    'class' => 'App:County',
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
