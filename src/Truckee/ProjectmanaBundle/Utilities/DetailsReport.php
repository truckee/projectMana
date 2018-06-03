<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\DetailsReport.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DetailsReport
 *
 * @author George
 */
class DetailsReport
{
    private $em;
    private $detailStatistics;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDetailStatistics($criteria)
    {
        $stats = $this->setDetailStatistics($criteria);

        return $stats;
    }

    private function setDetailStatistics($criteria)
    {
        $households = $this->em->getRepository('TruckeeProjectmanaBundle:Contact')->detailsHouseholds($criteria);
        $members = $this->em->getRepository('TruckeeProjectmanaBundle:Contact')->detailsMembers($criteria);
        $combined = array_merge($households, $members);
        //deconstruct combined array
        $collector = [];
        foreach ($combined as $array) {
            if (!array_key_exists($array['county'], $collector)) {
                $collector[$array['county']] = [];
                if (!array_key_exists($array['contactdesc'], $collector[$array['county']])) {
                    $collector[$array['county']][$array['contactdesc']] = [];
                }
            }
            if (array_key_exists('TH', $array)) {
                $collector[$array['county']][$array['contactdesc']]['TH'] = $array['TH'];
            }
            if (array_key_exists('UI', $array)) {
                $collector[$array['county']][$array['contactdesc']]['UI'] = $array['UI'];
            }
            if (array_key_exists('UH', $array)) {
                $collector[$array['county']][$array['contactdesc']]['UH'] = $array['UH'];
            }
            if (array_key_exists('TI', $array)) {
                $collector[$array['county']][$array['contactdesc']]['TI'] = $array['TI'];
            }
        }

        return $collector;
    }
}
