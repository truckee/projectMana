<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Services\GeneralStatisticsReport.php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of GeneralStatisticsReport
 *
 * @author George
 */
class GeneralStatisticsReport {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function getGeneralStats($criteria) {
        $stats = $this->setGeneralStats($criteria);

        return $stats;
    }

    private function setGeneralStats($criteria) {
        $statistics = [];
        $ageGenderData = $this->em->getRepository('App:Member')->ageEthnicityDistribution($criteria);
        $ageGenderDist = $this->setAgeGenderDist($ageGenderData);
        $ethDist = $this->setEthDist($ageGenderData);
        $sizeData = $this->em->getRepository('App:Household')->size($criteria);
        $householdResData = $this->em->getRepository('App:Household')->householdResidency($criteria);
        $residencyDist = $this->setResDist($sizeData, $householdResData);
        $familyDist = $this->setSizeDist($sizeData);
        $freqDist = $this->setFreqDist($criteria);

        $data = [
            $ageGenderDist['ageDist'],
            $ageGenderDist['ageGenderDist'],
            $familyDist,
            $freqDist,
            $ethDist,
            $residencyDist,
        ];
        foreach ($data as $statArray) {
            foreach ($statArray as $key => $value) {
                $statistics[$key] = $value;
            }
        }

        //unique new individuals
        $statistics['Unique New Individuals'] = $this->em->getRepository('App:Member')->uniqueNewMembers($criteria);

        //total individuals
        $tiData = $this->em->getRepository('App:Member')->reportMembers($criteria);
        $statistics['TIS'] = count($tiData);

        //total households
        $th = $this->em->getRepository('App:Household')->allHouseholds($criteria);
        $statistics['THS'] = count($th);

        //unique individuals & households
        $statistics['UIS'] = $statistics['UHS'] = 0;
        foreach ($sizeData as $hse) {
            $statistics['UIS'] += $hse['size'];
            $statistics['UHS'] ++;
        }

        //new by type
        $statistics['NewByType'] = $this->em->getRepository('App:Contact')->getNewByType($criteria);

        //newHouseholds
        $statistics['NewHouseholds'] = $this->em->getRepository('App:Contact')->uniqueHouseholds($criteria);

        return $statistics;
    }

    private function setSizeDist($data) {
        $familyArray = ['Single' => 0, 'Two' => 0, 'Three' => 0, 'Four' => 0, 'Five' => 0, 'Six or more' => 0];
        foreach ($data as $family) {
            switch ($family['size']) {
                case 1:
                    $familyArray['Single'] ++;
                    break;
                case 2:
                    $familyArray['Two'] ++;
                    break;
                case 3:
                    $familyArray['Three'] ++;
                    break;
                case 4:
                    $familyArray['Four'] ++;
                    break;
                case 5:
                    $familyArray['Five'] ++;
                    break;
                default:
                    $familyArray['Six or more'] ++;
                    break;
            }
        }

        return $familyArray;
    }

    private function setEthDist($data) {
        $ethnicities = $this->em->getRepository('App:Ethnicity')->findAll();
        foreach ($ethnicities as $object) {
            $eth[$object->getEthnicity()] = 0;
        }
        foreach ($data as $key => $value) {
            $eth[$value['ethnicity']] ++;
        }

        return $eth;
    }

    private function setResDist($sizeData, $householdResData) {
        $size = [];
        foreach ($sizeData as $array) {
            $size[$array['id']] = $array['size'];
        }
        $res = [];
        foreach ($householdResData as $array) {
            $res[$array['id']] = $array['R'];
        }

        /**
         * $size: key = id, value = size
         * $res: key = id, value = months
         */
        $resDist = ['< 1 month' => 0, '1 mo - 2 yrs' => 0, '>=2 yrs' => 0];

        if (count($res) <= count($size)) {
            // when $res has same # or fewer elements than $size
            foreach ($res as $key => $value) {
                if (0 === $value * 1) {
                    $resDist['< 1 month'] += $size[$key];
                } elseif (1 <= $value * 1 && $value * 1 < 24) {
                    $resDist['1 mo - 2 yrs'] += $size[$key];
                } elseif (24 < $value * 1) {
                    $resDist['>=2 yrs'] += $size[$key];
                }
            }
        } else {
            //when $size has fewer elements than $res
            foreach ($size as $key => $value) {
                if (0 === $res[$key] * 1) {
                    $resDist['< 1 month'] += $size[$key];
                } elseif (1 <= $res[$key] * 1 && $res[$key] * 1 < 24) {
                    $resDist['1 mo - 2 yrs'] += $size[$key];
                } elseif (24 < $res[$key] * 1) {
                    $resDist['>=2 yrs'] += $size[$key];
                }
            }
        }

        return $resDist;
    }

    private function setAgeGenderDist($data) {
        $ageDist = ['Under 6' => 0, '6 - 18' => 0, '19 - 59' => 0, '60+' => 0];
        $ageGenderDist = ['FC' => 0, 'MC' => 0, 'FA' => 0, 'MA' => 0, 'OC' => 0, 'OA' => 0,];
        foreach ($data as $row) {
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
                case $row['age'] < 6 && $row['sex'] == 'Other':
                    $ageDist['Under 6'] ++;
                    $ageGenderDist['OC'] ++;
                    break;
                case $row['age'] < 19 && $row['sex'] == 'Other':
                    $ageDist['6 - 18'] ++;
                    $ageGenderDist['OC'] ++;
                    break;
                case $row['age'] < 59 && $row['sex'] == 'Other':
                    $ageDist['19 - 59'] ++;
                    $ageGenderDist['OA'] ++;
                    break;
                case $row['age'] >= 60 && $row['sex'] == 'Other':
                    $ageDist['60+'] ++;
                    $ageGenderDist['OA'] ++;
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

    private function setFreqDist($criteria) {
        $frequency = ['1x' => 0, '2x' => 0, '3x' => 0, '4x' => 0];
        $qbSizes = $this->em->getRepository('App:Household')->size($criteria);
        foreach ($qbSizes as $row) {
            $sizes[$row['id']] = $row['size'];
        }
        $qbFreqs = $this->em->getRepository('App:Contact')->getHouseholdCount($criteria);
        foreach ($qbFreqs as $freq) {
            $household = $freq['id'];
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

}
