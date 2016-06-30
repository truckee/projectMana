<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\WorkRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WorkRepository.
 */
class WorkRepository extends EntityRepository
{
    public function rowLabels($dateCriteria)
    {
        $str = 'select distinct r.work 
        from work r
        join member m on m.work_id = r.id
        join household h on h.id = m.household_id
        join contact c on c.household_id = h.id
        WHERE c.contact_date BETWEEN __DATE_CRITERIA__
        order by work';
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $rowArray = $stmt->fetchAll();
        $rowLabels = [];
        foreach ($rowArray as $array) {
            $rowLabels[] = $array['work'];
        }

        return $rowLabels;
    }

    public function crossTabData($dateCriteria, $profileType)
    {
        $str = 'select ctr.__TYPE__ colLabel, w.work rowLabel, count(distinct m.id) N from member m
            join work w on m.work_id = w.id
            join household h on h.id = m.household_id
            join contact c on c.household_id = h.id
            join __TYPE__ ctr on ctr.id = c.__TYPE___id
            WHERE c.contact_date BETWEEN  __DATE_CRITERIA__
            GROUP BY colLabel, rowLabel';
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);

        return $stmt->fetchAll();
    }
}
