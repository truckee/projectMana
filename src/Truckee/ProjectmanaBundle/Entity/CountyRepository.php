<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\CountyRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

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
                ->from('TruckeeProjectmanaBundle:County', 'cty')
                ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.county = cty')
                ->where($criteria['betweenWhereClause'])
                ->setParameters($criteria['betweenParameters'])
                ->orderBy('cty.county')
                ->getQuery()->getResult();
    }
}
