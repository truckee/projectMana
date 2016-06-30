<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\NotfoodstampRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotfoodstampRepository.
 */
class NotfoodstampRepository extends EntityRepository
{
    public function rowLabels($dateCriteria)
    {
        $str = 'select distinct n.notfoodstamp
            FROM household h 
            JOIN contact c ON c.household_id = h.id 
            JOIN center r ON r.id = c.center_id 
            JOIN notfoodstamp n ON h.notfoodstamp_id = n.id 
            WHERE c.contact_date BETWEEN __DATE_CRITERIA__
            AND n.enabled = TRUE 
            order by notfoodstamp asc';
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $rowArray = $stmt->fetchAll();
        $rowLabels = [];
        foreach ($rowArray as $array) {
            $rowLabels[] = $array['notfoodstamp'];
        }

        return $rowLabels;
    }

    public function crossTabData($dateCriteria, $profileType)
    {
        $str = 'SELECT r.__TYPE__ colLabel, n.notfoodstamp rowLabel, COUNT(DISTINCT h.id) N 
            FROM household h 
            JOIN contact c ON c.household_id = h.id 
            JOIN __TYPE__ r ON r.id = c.__TYPE___id 
            JOIN notfoodstamp n ON h.notfoodstamp_id = n.id 
            WHERE c.contact_date BETWEEN __DATE_CRITERIA__
            AND n.enabled = TRUE 
            GROUP BY colLabel, rowLabel';
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);

        return $stmt->fetchAll();
    }
}
