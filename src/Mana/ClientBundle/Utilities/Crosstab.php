<?php

//src\Mana\ClientBundle\Utilities\Experiment.php

namespace Mana\ClientBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Description of Experiment
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
     * @param string $query = name of SQL statement string
     * @param array $rowArray ['keys', 'method']
     * @param array $colArray ['keys', 'method']
     * @return array
     */
    public function crosstabQuery($query, $rowArray, $colArray)
    {
        
        $profile = $this->profileArray($rowArray, $colArray);
        $conn = $this->em->getConnection();
        $queryResults = $conn->fetchAll($query);
        $profile = [];
        foreach ($queryResults as $array) {
            $profile[$array['rowValue']][$array['colValue']] = $array['N'];
        }
        
        return $profile; 
    }

    private function profileArray($rowArray, $colArray)
    {
        $rows = $rowArray['keys'];
        $rowFn = $rowArray['method'];
        $cols = $colArray['keys'];
        $colFn = $colArray['method'];
        $colKeys = [];
        foreach ($cols as $col) {
            $colValue = $col->$colFn();
            $colKeys[$colValue] = 0;
        }
        $profile = [];
        foreach ($rows as $row) {
            $rowValue = $row->$rowFn();
            $profile[$rowValue] = $colKeys;
        }
        
        return $profile;
    }
    
    /**
     * 
     * @param string $query
     * @param array $criteria ['startMonth', 'startYear', 'endMonth', 'endYear']
     * @return string
     */
    public function setDateCriteria($query, $criteria)
    {
        $startMonth = $criteria['startMonth'];
        $startYear = $criteria['startYear'];
        $startText = $startYear . '-' . $startMonth . '-' . '01';
        $endMonth = $criteria['endMonth'];
        $endYear = $criteria['endYear'];
        $endDate = new \DateTime($endMonth . '/01/' . $endYear);
        $endText = $endDate->format('Y-m-t');
        
        $dateCriteria = "'$startText' AND '$endText' ";
        
        return str_replace('__DATE_CRITERIA__', $dateCriteria, $query);
        
    }
}
