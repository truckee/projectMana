<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Repository\WorkRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * WorkRepository.
 */
class WorkRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->createQueryBuilder('w')
            ->select('w.job')
            ->distinct()
            ->join('w.members', 'm')
            ->join('App:Household', 'h', 'WITH',  'h.head = m')
            ->join('h.contacts', 'c')
            ->where($criteria['betweenWhereClause'])
            ->orderBy('w.job')
            ->setParameters($criteria['betweenParameters'])
            ->getQuery()->getResult()
            ;
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['job'];
        }

        return $rowLabels;
    }

    /**
     * Get profile data
     *
     * @param array $dateCriteria
     * @param array $profileType
     *
     * @return array
     */
    public function crossTabData($criteria)
    {
        $profileType = $criteria['columnType'];

        return $this->createQueryBuilder('w')
            ->select('r.' . $profileType . ' colLabel, w.job rowLabel, COUNT(DISTINCT h.id) N ')
            ->join('w.members', 'm')
            ->join('App:Household', 'h', 'WITH',  'h.head = m')
            ->join('h.contacts', 'c')
                ->join('c.' . $profileType, 'r')
                ->where($criteria['betweenWhereClause'])
                ->groupBy('colLabel, rowLabel')
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult();
    }
}
