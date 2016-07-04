<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\ReasonRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ReasonRepository.
 */
class ReasonRepository extends EntityRepository
{
    public function rowLabels($dateCriteria)
    {
        $str = 'select distinct r.reason 
        from reason r
        join household_reason hr on hr.reason_id = r.id
        join household h on h.id = hr.household_id
        join contact c on c.household_id = h.id
        WHERE c.contact_date BETWEEN __DATE_CRITERIA__
        order by reason';
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $rowArray = $stmt->fetchAll();
        $rowLabels = [];
        foreach ($rowArray as $array) {
            $rowLabels[] = $array['reason'];
        }

        return $rowLabels;
    }

    public function crossTabData($dateCriteria, $profileType)
    {
        $str = 'select ctr.__TYPE__ colLabel, r.reason rowLabel, count(distinct h.id) N from household h
            join contact c on c.household_id = h.id
            join __TYPE__ ctr on ctr.id = c.__TYPE___id
            join household_reason hr on hr.household_id = h.id
            join reason r on r.id = hr.reason_id
            WHERE c.contact_date BETWEEN  __DATE_CRITERIA__
            GROUP BY colLabel, rowLabel';
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);

        return $stmt->fetchAll();
    }
}
