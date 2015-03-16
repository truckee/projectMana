<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\IncomeRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * IncomeRepository
 *
 */
class IncomeRepository extends EntityRepository
{

    public function rowLabels($dateCriteria)
    {
        $str = "select distinct i.income 
            from income i
            join household h on h.income_id = i.id
            join contact c on c.household_id = h.id 
            WHERE c.contact_date BETWEEN __DATE_CRITERIA__
            AND i.enabled = TRUE order by i.id ";
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $rowArray = $stmt->fetchAll();
        $rowLabels = [];
        foreach ($rowArray as $array) {
            $rowLabels[] = $array['income'];
        }
        
        return $rowLabels;
    }

    public function crossTabData($dateCriteria, $profileType)
    {
        $str = "SELECT r.__TYPE__ colLabel, i.income rowLabel, COUNT(DISTINCT h.id) N " .
                "FROM household h " .
                "JOIN contact c ON c.household_id = h.id " .
                "LEFT JOIN __TYPE__ r ON r.id = c.__TYPE___id " .
                "LEFT JOIN income i ON h.income_id = i.id " .
                "WHERE c.contact_date BETWEEN __DATE_CRITERIA__ " .
                "AND i.enabled = TRUE " .
                "GROUP BY colLabel, rowLabel";
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        
        return $stmt->fetchAll();
    }

}
