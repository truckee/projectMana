<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Repository\BenefitRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of BenefitRepository
 *
 */
class BenefitRepository extends EntityRepository
{

    public function crossTabData($criteria)
    {
        $qbYes = $qbAll = $qbNo = [];
        $profileType = $criteria['columnType'];
        $parameters = array_merge($criteria['betweenParameters'], ['snap' => 'SNAP']);
        $qb1 = $this->getEntityManager()->createQueryBuilder()
                ->select('r.' . $profileType . ' colLabel, COUNT(distinct h.id) N')
                ->from('App:Household', 'h')
                ->join('h.contacts', 'c')
                ->join('c.' . $profileType, 'r')
                ->join('h.benefits', 'b')
                ->where($criteria['betweenWhereClause'])
                ->andWhere('b.benefit = :snap')
                ->groupBy('colLabel')
                ->setParameters($parameters)
                ->getQuery()->getResult()
        ;

        foreach ($qb1 as $key => $value) {
            $qbYes[] = array_merge(['rowLabel' => 'Yes'], $value);
        }
        $qbAll = $this->getEntityManager()->createQueryBuilder()
                ->select('r.' . $profileType . ' colLabel, COUNT(distinct h.id) N')
                ->from('App:Household', 'h')
                ->join('h.contacts', 'c')
                ->join('c.' . $profileType, 'r')
                ->where($criteria['betweenWhereClause'])
                ->groupBy('colLabel')
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
        foreach ($qbAll as $key => $no) {
            $colLabel = $no['colLabel'];
            foreach ($qbYes as $key => $yes) {
                if ($yes['colLabel'] === $colLabel) {
                    $no['N'] -= $yes['N'];
                    $qbNo[] = array_merge(['rowLabel' => 'No'], $no);
                }
            }
        }

        return array_merge($qbYes, $qbNo);
    }
}
