<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\GeneralStatisticsReport.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of GeneralStatisticsReport
 *
 * @author George
 */
class GeneralStatisticsReport
{
    private $em;
    private $reportCriteria;
    private $tableCriteria;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getGeneralStats($tableCriteria, $reportCriteria)
    {
        $this->tableCriteria = $tableCriteria;
        $this->reportCriteria = $reportCriteria;
        $statistics = array();

        $ageGenderDist = $this->setAgeGenderDist();
        $residency = $this->setResidency();
        $familyDist = $this->setFamilyDist();
        $freqDist = $this->setFreqDist();
        $ethDist = $this->setEthDist();

        $data = [
            $ageGenderDist['ageDist'],
            $ageGenderDist['ageGenderDist'],
            $residency,
            $familyDist,
            $freqDist,
            $ethDist,
        ];

        foreach ($data as $statArray) {
            foreach ($statArray as $key => $value) {
                $statistics[$key] = $value;
            }
        }

        //unique new individuals
        $uniqNewInd = $this->em->createQuery('SELECT COUNT(m) UNI FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . 'WHERE c.first = true')
            ->getSingleScalarResult();
        $statistics['Unique New Individuals'] = (!empty($uniqNewInd)) ? $uniqNewInd
                : 0;

        //total individuals
        $ti = $this->em->createQuery("SELECT SUM(h.size) TIS FROM TruckeeProjectmanaBundle:TempHousehold h "
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH c.household = h')->getSingleScalarResult();
        $statistics['TIS'] = (!empty($ti)) ? $ti : 0;

        //unique individuals
        $uis = $this->em->createQuery('SELECT SUM(h.size) UIS FROM TruckeeProjectmanaBundle:TempHousehold h')->getSingleScalarResult();
        $statistics['UIS'] = (!empty($uis)) ? $uis : 0;

        //total households
        $th = $this->em->createQuery('SELECT COUNT(c.household) THS FROM TruckeeProjectmanaBundle:TempContact c ')
            ->getSingleScalarResult();
        $statistics['THS'] = (!empty($th)) ? $th : 0;

        //unique households
        $uhs = $this->em->createQuery('SELECT COUNT(h) UHS FROM TruckeeProjectmanaBundle:TempHousehold h')
            ->getSingleScalarResult();
        $statistics['UHS'] = (!empty($uhs)) ? $uhs : 0;

        //new members
        $newMembers = $this->em->createQuery('SELECT COUNT(m) NewMembers FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $this->tableCriteria['newWhereClause'] . " AND c.first = true")
            ->setParameters($this->tableCriteria['parameters'])
            ->getSingleScalarResult();
        $statistics['NewMembers'] = (!empty($newMembers)) ? $newMembers : 0;

        //new households
        $newHouseholds = $this->em->createQuery('SELECT COUNT(DISTINCT c.household) NewHouseholds FROM TruckeeProjectmanaBundle:TempContact c '
                . 'WHERE c.first = true')
            ->getSingleScalarResult();
        $statistics['NewHouseholds'] = (!empty($newHouseholds)) ? $newHouseholds
                : 0;

        //new by type
        $nbt = $this->em->createQuery('SELECT COUNT(DISTINCT c.household) NewByType FROM TruckeeProjectmanaBundle:TempContact c '
                . $this->tableCriteria['newWhereClause'] . " AND c.first = true")
            ->setParameters($this->tableCriteria['parameters'])
            ->getSingleScalarResult();
        $statistics['NewByType'] = (!empty($nbt)) ? $nbt : 0;

        return $statistics;
    }

    private function setAgeGenderDist()
    {
        $ageDist = ['Under 6' => 0, '6 - 18' => 0, '19 - 59' => 0, '60+' => 0];
        $ageGenderDist = ['FC' => 0, 'MC' => 0, 'FA' => 0, 'MA' => 0];
        $qb = $this->em->createQuery('SELECT m.age, m.sex FROM TruckeeProjectmanaBundle:TempMember m')->getResult();
        foreach ($qb as $row) {
            switch (true) {
                case $row['age'] < 6 && $row['sex'] == 'Female':
                    $ageDist['Under 6'] ++;
                    $ageGenderDist['FC'] ++;
                    break;
                case $row['age'] < 19 && $row['sex'] == 'Female':
                    $ageDist['6 - 18'] ++;
                    $ageGenderDist['FC'] ++;
                    break;
                case $row['age'] < 59 && $row['sex'] == 'Female':
                    $ageDist['19 - 59'] ++;
                    $ageGenderDist['FA'] ++;
                    break;
                case $row['age'] >= 60 && $row['sex'] == 'Female':
                    $ageDist['60+'] ++;
                    $ageGenderDist['FA'] ++;
                    break;
                case $row['age'] < 6 && $row['sex'] == 'Male':
                    $ageDist['Under 6'] ++;
                    $ageGenderDist['MC'] ++;
                    break;
                case $row['age'] < 19 && $row['sex'] == 'Male':
                    $ageDist['6 - 18'] ++;
                    $ageGenderDist['MC'] ++;
                    break;
                case $row['age'] < 59 && $row['sex'] == 'Male':
                    $ageDist['19 - 59'] ++;
                    $ageGenderDist['MA'] ++;
                    break;
                case $row['age'] >= 60 && $row['sex'] == 'Male':
                    $ageDist['60+'] ++;
                    $ageGenderDist['MA'] ++;
                    break;

                default:
                    break;
            }
        }

        return [
            'ageDist' => $ageDist,
            'ageGenderDist' => $ageGenderDist
        ];
    }

    private function setEthDist()
    {
        $qb = $this->em->createQuery('SELECT e FROM TruckeeProjectmanaBundle:Ethnicity e')->getResult();
        foreach ($qb as $row) {
            $queryEth = $this->em->createQuery('SELECT COUNT(m) N FROM TruckeeProjectmanaBundle:TempMember m '
                    . 'JOIN TruckeeProjectmanaBundle:TempHousehold h WITH m.household = h '
                    . 'WHERE (m <> h.head OR (m = h.head AND m.age IS NOT NULL)) AND m.ethnicity = :eth')
                ->setParameter('eth', $row)
                ->getSingleResult();
            $ethDist[$row->getEthnicity()] = $queryEth['N'];
        }

        return $ethDist;
    }

    private function setFamilyDist()
    {
        $familyArray = ['Single', 'Two', 'Three', 'Four', 'Five', 'Six or more'];
        $qb = $this->em->createQuery('SELECT COUNT(h.size) FROM TruckeeProjectmanaBundle:TempHousehold h '
            . 'WHERE h.sizeText = :size');
        foreach ($familyArray as $size) {
            $n = $qb->setParameter('size', $size)->getSingleScalarResult();
            $familyDist[$size] = (null === $n) ? 0 : $n;
        }

        return $familyDist;
    }

    private function setFreqDist()
    {
        $frequency = ['1x' => 0, '2x' => 0, '3x' => 0, '4x' => 0];
        $qbSizes = $this->em->createQuery('SELECT h.id, h.size S FROM TruckeeProjectmanaBundle:TempHousehold h')->getResult();
        foreach ($qbSizes as $row) {
            $sizes[$row['id']] = $row['S'];
        }
        $qbFreqs = $this->em->createQuery('SELECT c.household, COUNT(c) N FROM TruckeeProjectmanaBundle:TempContact c '
                . 'GROUP BY c.household')->getResult();
        foreach ($qbFreqs as $freq) {
            $household = $freq['household'];
            $size = $sizes[$household];
            switch ($freq['N']) {
                case 1:
                    $frequency['1x'] += $size;
                    break;
                case 2:
                    $frequency['2x'] += $size;
                    break;
                case 3:
                    $frequency['3x'] += $size;
                    break;
                default:
                    $frequency['4x'] += $size;
                    break;
            }
        }

        return $frequency;
    }

    private function setResidency()
    {
        $residency = [];
        $resArray = ['< 1 month', '1 mo - 2 yrs', '>=2 yrs'];
        $qb = $this->em->createQuery('SELECT SUM(h.size) FROM TruckeeProjectmanaBundle:TempHousehold h '
            . 'WHERE h.res = :res');
        foreach ($resArray as $res) {
            $n = $qb->setParameter('res', $res)->getSingleScalarResult();
            $residency[$res] = (null === $n) ? 0 : $n;
        }

        return $residency;
    }
}
