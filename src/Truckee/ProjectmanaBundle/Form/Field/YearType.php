<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src\Truckee\ProjectmanaBundle\Resources\views\Form\Field\YearType.php

namespace Truckee\ProjectmanaBundle\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class YearType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $range = range(Date('Y'), Date('Y') - 50, -1);
        $years = array();
        foreach ($range as $year) {
            $years[$year] = $year;
        }
        $resolver->setDefaults(array(
            'choices' => $years,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
