<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\App\Repository\CountyRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CountyRepository.
 */
class CountyRepository extends EntityRepository
{
    public function countiesForStats($criteria)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('cty')
                ->distinct()
                ->from('App:County', 'cty')
                ->join('cty.contacts', 'c')
                ->where($criteria['betweenWhereClause'])
                ->setParameters($criteria['betweenParameters'])
                ->orderBy('cty.county')
                ->getQuery()->getResult();
    }
}
