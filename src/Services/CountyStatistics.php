<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Services\CountyStatistics.php

namespace App\Services;

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
        $counties = $this->em->getRepository('App:County')->countiesForStats($criteria);
        $uisTotal = $uhsTotal = $tisTotal = $thsTotal = 0;
        $county = [];
        $criteria['siteWhereClause'] = 'c.county = :county';
        foreach ($counties as $site) {
            $criteria['siteParameters'] = ['county' => $site];

            $county[$site->getCounty()]['UIS'] = 0;
            $county[$site->getCounty()]['UHS'] = 0;
            //household data
            $sizeData = $this->em->getRepository('App:Household')->size($criteria);
            foreach ($sizeData as $hse) {
                $county[$site->getCounty()]['UIS'] += $hse['size'];
                $uisTotal += $hse['size'];
                $county[$site->getCounty()]['UHS'] ++;
                $uhsTotal ++;
            }
            $tiData = $this->em->getRepository('App:Member')->reportMembers($criteria);
            $county[$site->getCounty()]['TIS'] = count($tiData);
            $tisTotal += count($tiData);
            $thData = $this->em->getRepository('App:Household')->allHouseholds($criteria);
            $county[$site->getCounty()]['THS'] = count($thData);
            $thsTotal += count($thData);
        }
        foreach (array_keys($county) as $value) {
            $county[$value]['UISPCT'] = (0 < $uisTotal) ? 100 * $county[$value]['UIS'] / $uisTotal : 0;
            $county[$value]['UHSPCT'] = (0 < $uhsTotal) ? 100 * $county[$value]['UHS'] / $uhsTotal : 0;
            $county[$value]['TISPCT'] = (0 < $tisTotal) ? 100 * $county[$value]['TIS'] / $tisTotal : 0;
            $county[$value]['THSPCT'] = (0 < $thsTotal) ? 100 * $county[$value]['THS'] / $thsTotal : 0;
        }

        return $statistics['countyStats'] = $county;
    }
}
