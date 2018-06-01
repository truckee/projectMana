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
        $statistics['UIS_Total'] = 0;
        $statistics['UHS_Total'] = 0;
        $statistics['TIS_Total'] = 0;
        $statistics['THS_Total'] = 0;
        $criteria['siteWhereClause'] = 'c.county = :county';
        foreach ($counties as $site) {
            $criteria['siteParameters'] = ['county' => $site];
            $statistics[$site->getCounty()]['UIS'] = 0;
            $statistics[$site->getCounty()]['UHS'] = 0;
            //household data
            $sizeData = $this->em->getRepository('TruckeeProjectmanaBundle:Household')->size($criteria);
            foreach ($sizeData as $hse) {
                $statistics[$site->getCounty()]['UIS'] += $hse['size'];
                $statistics['UIS_Total'] += $hse['size'];
                $statistics[$site->getCounty()]['UHS'] ++;
                $statistics['UHS_Total'] ++;
            }
            $tiData = $this->em->getRepository('TruckeeProjectmanaBundle:Member')->reportMembers($criteria);
            $statistics[$site->getCounty()]['TIS'] = count($tiData);
            $statistics['TIS_Total'] += count($tiData);
            $thData = $this->em->getRepository('TruckeeProjectmanaBundle:Household')->reportHousehold($criteria);
            $statistics[$site->getCounty()]['THS'] = count($thData);
            $statistics['THS_Total'] += count($thData);
        }

        return $statistics;
    }

    public function OldgetCountyStats()
    {
        $joinPhrase = 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty GROUP BY cty.county';
        $db = $this->conn;
        $UISData = $this->em->createQuery('SELECT cty.county, COUNT(DISTINCT m) UIS FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $joinPhrase)->getResult();

        $UHSData = $this->em->createQuery('SELECT cty.county, COUNT(DISTINCT c.household) UHS FROM TruckeeProjectmanaBundle:TempContact c '
                . $joinPhrase)->getResult();


        $TISData = $this->em->createQuery('SELECT cty.county, COUNT(m) TIS FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $joinPhrase)->getResult();

        $THSData = $this->em->createQuery('SELECT cty.county, COUNT(c.household) THS FROM TruckeeProjectmanaBundle:TempContact c '
                . $joinPhrase)->getResult();

        $totals['UISTotal'] = 0;
        $totals['UHSTotal'] = 0;
        $totals['TISTotal'] = 0;
        $totals['THSTotal'] = 0;

        if (empty($UISData)) {
            $sqlCounties = 'select county from county where enabled = 1 order by county asc';
            $counties = $db->query($sqlCounties)->fetchAll();
            $categories = ['UIS', 'TIS', 'UHS', 'THS'];
            foreach ($categories as $category) {
                $varname = $category . 'Data';
                foreach ($counties as $county) {
                    ${$varname}[] = array('county' => $county['county'], $category => 0);
                }
            }
        }
        foreach ($UISData as $array) {
            $totals['UIS'][$array['county']] = $array['UIS'];
            $totals['UISTotal'] += $array['UIS'];
        }
        foreach ($UHSData as $array) {
            $totals['UHS'][$array['county']] = $array['UHS'];
            $totals['UHSTotal'] += $array['UHS'];
        }
        foreach ($TISData as $array) {
            $totals['TIS'][$array['county']] = $array['TIS'];
            $totals['TISTotal'] += $array['TIS'];
        }
        foreach ($THSData as $array) {
            $totals['THS'][$array['county']] = $array['THS'];
            $totals['THSTotal'] += $array['THS'];
        }

        $data = [];
        foreach ($UISData as $array) {
            $data[$array['county']]['UIS'] = $array['UIS'];
            $data[$array['county']]['UISPCT'] = (0 < $totals['UISTotal']) ? sprintf(
                    '%01.1f', 100 * ($array['UIS'] / $totals['UISTotal'])
                ) . '%' : 0;
        }
        foreach ($UHSData as $array) {
            $data[$array['county']]['UHS'] = $array['UHS'];
            $data[$array['county']]['UHSPCT'] = (0 < $totals['UHSTotal']) ? sprintf(
                    '%01.1f', 100 * ($array['UHS'] / $totals['UHSTotal'])
                ) . '%' : 0;
        }
        foreach ($TISData as $array) {
            $data[$array['county']]['TIS'] = $array['TIS'];
            $data[$array['county']]['TISPCT'] = (0 < $totals['TISTotal']) ? sprintf(
                    '%01.1f', 100 * ($array['TIS'] / $totals['TISTotal'])
                ) . '%' : 0;
        }
        foreach ($THSData as $array) {
            $data[$array['county']]['THS'] = $array['THS'];
            $data[$array['county']]['THSPCT'] = (0 < $totals['THSTotal']) ? sprintf(
                    '%01.1f', 100 * ($array['THS'] / $totals['THSTotal'])
                ) . '%' : 0;
        }

        return $data;
    }
}
