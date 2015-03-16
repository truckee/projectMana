<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\FsAmountRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FsAmountRepository
 *
 */
class FsAmountRepository extends EntityRepository
{

    public function rowLabels($dateCriteria)
    {
        $str = "select distinct f.amount
            FROM household h 
            JOIN contact c ON c.household_id = h.id 
            JOIN center r ON r.id = c.center_id 
            JOIN fs_amount f ON h.fsamount_id = f.id 
            WHERE c.contact_date BETWEEN __DATE_CRITERIA__
            AND f.enabled = TRUE 
            order by amount asc";
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $rowArray = $stmt->fetchAll();
        $rowLabels = [];
        foreach ($rowArray as $array) {
            $rowLabels[] = $array['amount'];
        }
        
        return $rowLabels;
    }
    
    public function crossTabData($dateCriteria, $profileType)
    {
        $str = "SELECT r.__TYPE__ colLabel, f.amount rowLabel, COUNT(DISTINCT h.id) N 
            FROM household h 
            JOIN contact c ON c.household_id = h.id 
            JOIN __TYPE__ r ON r.id = c.__TYPE___id 
            JOIN fs_amount f ON h.fsamount_id = f.id 
            WHERE c.contact_date BETWEEN __DATE_CRITERIA__
            AND f.enabled = TRUE 
            GROUP BY colLabel, rowLabel";
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        
        return $stmt->fetchAll();
    }
    
}
