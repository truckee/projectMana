<?php
// src/Mana/ClientBundle/Resources/views/Form/DataTransformer/AgeToDOB.php

namespace Truckee\ProjectmanaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

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
        if (is_numeric($dob)) {
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
