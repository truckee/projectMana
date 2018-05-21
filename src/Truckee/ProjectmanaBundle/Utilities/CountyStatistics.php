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
    private $conn;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->conn = $em->getConnection();
    }

    public function getCountyStats()
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
                    '%01.1f',
                100 * ($array['UIS'] / $totals['UISTotal'])
                ) . '%' : 0;
        }
        foreach ($UHSData as $array) {
            $data[$array['county']]['UHS'] = $array['UHS'];
            $data[$array['county']]['UHSPCT'] = (0 < $totals['UHSTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['UHS'] / $totals['UHSTotal'])
                ) . '%' : 0;
        }
        foreach ($TISData as $array) {
            $data[$array['county']]['TIS'] = $array['TIS'];
            $data[$array['county']]['TISPCT'] = (0 < $totals['TISTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['TIS'] / $totals['TISTotal'])
                ) . '%' : 0;
        }
        foreach ($THSData as $array) {
            $data[$array['county']]['THS'] = $array['THS'];
            $data[$array['county']]['THSPCT'] = (0 < $totals['THSTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['THS'] / $totals['THSTotal'])
                ) . '%' : 0;
        }

        return $data;
    }
}
