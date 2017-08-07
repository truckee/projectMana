<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\Addresses.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Addresses
 *
 */
class Addresses
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function addressTemplates($household)
    {
        $addresses = $household->getAddresses();
        $physical = 'Address/physicalAddressBlock.html.twig';
        $mailing = 'Address/mailingAddressBlock.html.twig';
        $type = '';
        switch (count($addresses)) {
            case 0:
                $templates[] = $physical;
                $templates[] = $mailing;
                break;
            case 1:
                $type = $addresses[0]->getAddressType()->getAddressType();
                $templates[] = ('Physical' === $type) ? $mailing : $physical;
                // no break
            default:
                $templates[] = 'Address/existingAddressBlock.html.twig';
                break;
        }

        return $templates;
    }
}
