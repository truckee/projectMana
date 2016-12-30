<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src\Truckee\ProjectmanaBundle\Resources\views\Form\Field\MonthType.php

namespace Truckee\ProjectmanaBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MonthType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $months = array(
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );

        $resolver->setDefaults(array(
            'choices' => array_flip($months),
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
