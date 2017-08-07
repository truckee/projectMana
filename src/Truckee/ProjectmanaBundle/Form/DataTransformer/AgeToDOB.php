<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Mana/ClientBundle/Resources/views/Form/DataTransformer/AgeToDOB.php

namespace Truckee\ProjectmanaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Converts age to estimate of DOB
 *
 * Transformer required because initial design of Project MANA
 * database did not track date of birth.
 *
 */
class AgeToDOB implements DataTransformerInterface
{
    public function reverseTransform($dob)
    {
        if (null == $dob || '' == $dob) {
            return;
        }
        if ((substr_count($dob, '/') == 2 && strtotime($dob))) {
            $date = new \DateTime($dob);

            return $date;
        }
        if (is_numeric($dob) && is_int(1 * $dob)) {
            $date = new \DateTime();
            $interval = 'P'.$dob.'Y';
            $date->sub(new \DateInterval($interval));

            return $date;
        }

        return;
    }

    public function transform($client)
    {
        if (null == $client) {
            return '';
        }
        if (is_object($client)) {
            return date_format($client, 'm/d/Y');
        }
    }
}
