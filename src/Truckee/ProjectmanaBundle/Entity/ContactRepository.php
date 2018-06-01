<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\ContactRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

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
                . 'r.center, c1.contactDate, d.contactdesc FROM TruckeeProjectmanaBundle:Contact c1 '
                . 'JOIN TruckeeProjectmanaBundle:Contact c2 WITH c1.household = c2.household '
                . 'JOIN TruckeeProjectmanaBundle:Household h WITH c1.household = h '
                . 'JOIN TruckeeProjectmanaBundle:Member m WITH m = h.head '
                . 'JOIN TruckeeProjectmanaBundle:Center r WITH c1.center = r '
                . 'JOIN TruckeeProjectmanaBundle:Contactdesc d WITH c1.contactdesc = d '
                . 'WHERE c1.contactDate = c2.contactDate '
                . 'AND c1 <> c2 '
                . 'AND c1.contactDate >= :startDate AND c1.contactDate <= :endDate')
            ->setParameters(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']])
            ->getResult();

        return $qb;
    }

    public function getHouseholdCount($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters']);

        return $this->getEntityManager()->createQueryBuilder()
                ->select('IDENTITY(c.household) id, count(IDENTITY(c.household)) N')
                ->from('TruckeeProjectmanaBundle:Contact', 'c')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($criteria['contactWhereClause'])
                ->setParameters($parameters)
                ->groupBy('id')
                ->orderBy('id')
                ->getQuery()->getResult();
    }

    public function getNewByType($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters']);

        return $this->getEntityManager()->createQueryBuilder()
                ->select('count(distinct IDENTITY(c.household))')
                ->from('TruckeeProjectmanaBundle:Contact', 'c')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($criteria['contactWhereClause'])
                ->andWhere('c.first = true')
                ->setParameters($parameters)
                ->getQuery()->getSingleScalarResult();
    }
}
