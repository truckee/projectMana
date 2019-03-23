<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Repository\ContactRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ContactRepository
 *
 * @author George
 */
class ContactRepository extends EntityRepository
{

    /**
     * Get set of households with multiple same-date contacts
     *
     * @param type $criteria
     *
     * @return array
     */
    public function getMultiContacts($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT IDENTITY(c1.household) id, m.fname, m.sname, '
                . 'r.center, c1.contactDate, d.contactdesc FROM App:Contact c1 '
                . 'JOIN App:Contact c2 WITH c1.household = c2.household '
                . 'JOIN App:Household h WITH c1.household = h '
                . 'JOIN App:Member m WITH m = h.head '
                . 'JOIN App:Center r WITH c1.center = r '
                . 'JOIN App:Contactdesc d WITH c1.contactdesc = d '
                . 'WHERE c1.contactDate = c2.contactDate '
                . 'AND c1 <> c2 '
                . 'AND c1.contactDate >= :startDate AND c1.contactDate <= :endDate')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }

    public function getHouseholdCount($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters']);

        return $this->getEntityManager()->createQueryBuilder()
                ->select('IDENTITY(c.household) id, count(IDENTITY(c.household)) N')
                ->from('App:Contact', 'c')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($criteria['contactWhereClause'])
                ->setParameters($parameters)
                ->groupBy('id')
                ->orderBy('id')
                ->getQuery()->getResult();
    }

    public function uniqueHouseholds($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters']);

        return $this->uniqueHouseholdsQuery($criteria)
                ->andWhere('c.first = true')
                ->setParameters($parameters)
                ->getQuery()->getSingleScalarResult();
    }

    public function getNewByType($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters']);

        return $this->uniqueHouseholdsQuery($criteria)
                ->andWhere('c.first = true')
                ->andWhere($criteria['contactWhereClause'])
                ->setParameters($parameters)
                ->getQuery()->getSingleScalarResult();
    }

    public function detailsHouseholds($criteria)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('cty.county, cd.contactdesc, count(IDENTITY(c.household)) THS, count(distinct IDENTITY(c.household)) UHS')
                ->from('App:Contact', 'c')
                ->join('c.county', 'cty')
                ->join('c.contactdesc', 'cd')
                ->where($criteria['betweenWhereClause'])
                ->groupBy('cty.county, cd.contactdesc')
                ->orderBy('cty.county, cd.contactdesc')
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult();
    }

    public function detailsMembers($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['startParameters'], $criteria['startParameters']);
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $this->getEntityManager()->createQueryBuilder()
                ->select('cty.county, cd.contactdesc, count(m.id) TIS, count(distinct m.id) UIS')
                ->from('App:Contact', 'c')
                ->join('c.household', 'h')
                ->join('h.members', 'm')
                ->join('c.county', 'cty')
                ->join('c.contactdesc', 'cd')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($qb->expr()->orX('m.excludeDate > :startDate', $qb->expr()->isNull('m.excludeDate')))
                ->andWhere($qb->expr()->orX('m.dob < :startDate', $qb->expr()->isNull('m.dob')))
                ->groupBy('cty.county, cd.contactdesc')
                ->orderBy('cty.county, cd.contactdesc')
                ->setParameters($parameters)
                ->getQuery()->getResult();
    }

    private function uniqueHouseholdsQuery($criteria)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('count(distinct IDENTITY(c.household))')
                ->from('App:Contact', 'c')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause']);
    }
}
