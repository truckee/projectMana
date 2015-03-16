<?php

//src\Mana\ClientBundle\Utilities\Experiment.php

namespace Mana\ClientBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Description of Crosstab
 *
 * @author George
 */
class Crosstab
{
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * 
     * @param array $data = data array
     * @param array $rowLabels
     * @param array $colLabels
     * @return array
     */
    public function crosstabQuery($data, $rowLabels, $colLabels)
    {
        $profile = $this->profileArray( $rowLabels, $colLabels);
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
     * @param string $query
     * @param array $criteria ['startMonth', 'startYear', 'endMonth', 'endYear']
     * @return string
     */
    public function setDateCriteria($criteria)
    {
        $startMonth = $criteria['startMonth'];
        $startYear = $criteria['startYear'];
        $startText = $startYear . '-' . $startMonth . '-' . '01';
        $endMonth = $criteria['endMonth'];
        $endYear = $criteria['endYear'];
        $endDate = new \DateTime($endMonth . '/01/' . $endYear);
        $endText = $endDate->format('Y-m-t');
        
        return "'$startText' AND '$endText' ";
//        $dateCriteria = "'$startText' AND '$endText' ";
        
//        return str_replace('__DATE_CRITERIA__', $dateCriteria, $query);
        
    }
}
