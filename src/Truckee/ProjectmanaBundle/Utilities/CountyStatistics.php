<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\CountyStatistics.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of CountyStatistics
 *
 * @author George
 */
class CountyStatistics
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCountyStats($criteria)
    {
        $counties = $this->em->getRepository('TruckeeProjectmanaBundle:County')->countiesForStats($criteria);
        $uisTotal = $uhsTotal = $tisTotal = $thsTotal = 0;
        $county = [];
        $criteria['siteWhereClause'] = 'c.county = :county';
        foreach ($counties as $site) {
            $criteria['siteParameters'] = ['county' => $site];

            $county[$site->getCounty()]['UIS'] = 0;
            $county[$site->getCounty()]['UHS'] = 0;
            //household data
            $sizeData = $this->em->getRepository('TruckeeProjectmanaBundle:Household')->size($criteria);
            foreach ($sizeData as $hse) {
                $county[$site->getCounty()]['UIS'] += $hse['size'];
                $uisTotal += $hse['size'];
                $county[$site->getCounty()]['UHS'] ++;
                $uhsTotal ++;
            }
            $tiData = $this->em->getRepository('TruckeeProjectmanaBundle:Member')->reportMembers($criteria);
            $county[$site->getCounty()]['TIS'] = count($tiData);
            $tisTotal += count($tiData);
            $thData = $this->em->getRepository('TruckeeProjectmanaBundle:Household')->reportHousehold($criteria);
            $county[$site->getCounty()]['THS'] = count($thData);
            $thsTotal += count($thData);
        }
        foreach ($county as $key => $value) {
            $county[$key]['UISPCT'] = (0 < $uisTotal) ? 100 * $county[$key]['UIS'] / $uisTotal : 0;
            $county[$key]['UHSPCT'] = (0 < $uhsTotal) ? 100 * $county[$key]['UHS'] / $uhsTotal : 0;
            $county[$key]['TISPCT'] = (0 < $tisTotal) ? 100 * $county[$key]['TIS'] / $tisTotal : 0;
            $county[$key]['THSPCT'] = (0 < $thsTotal) ? 100 * $county[$key]['THS'] / $thsTotal : 0;
        }

        return $statistics['countyStats'] = $county;
        ;
    }
}
