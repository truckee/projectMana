<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\Experiment.php

namespace Truckee\ProjectmanaBundle\Utilities;

/**
 * Description of Crosstab.
 *
 * @author George
 */
class Crosstab
{
    /**
     * @param array $data      = data array
     * @param array $rowLabels
     * @param array $colLabels
     *
     * @return array
     */
    public function crosstabQuery($data, $rowLabels, $colLabels)
    {
        $profile = $this->profileArray($rowLabels, $colLabels);
        foreach ($data as $array) {
            if (!array_key_exists('total', $profile[$array['rowLabel']])) {
                $profile[$array['rowLabel']]['total'] = 0;
            }
            $profile[$array['rowLabel']][$array['colLabel']] = $array['N'];
            $profile[$array['rowLabel']]['total'] += $array['N'];
        }
        foreach ($profile as $key => $array) {
            if (!array_key_exists('total', $array)) {
                $profile[$key]['total'] = 0;
            }
        }
        $profile['total'] = [];
        foreach ($profile as $row => $array) {
            foreach ($array as $key => $value) {
                if (!array_key_exists($key, $profile['total'])) {
                    $profile['total'][$key] = 0;
                }
                $profile['total'][$key] += $value;
            }
        }

        return $profile;
    }

    private function profileArray($rows, $cols)
    {
        $colKeys = [];
        foreach ($cols as $col) {
            $colKeys[$col] = 0;
        }
        $profile = [];
        foreach ($rows as $row) {
            $profile[$row] = $colKeys;
        }

        return $profile;
    }

    /**
     *
     * @return array
     */
    public function setDateCriteria($criteria)
    {
        return ['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']];
    }
}
