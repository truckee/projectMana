<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MemberRepository.
 *
 * @author George Brooks
 */
class MemberRepository extends EntityRepository
{

    /**
     * Default  member surname = household head's surname
     *
     * @param object $household
     */
    public function initialize($household)
    {
        $em = $this->getEntityManager();
        $members = $household->getMembers();
        $sname = $household->getHead()->getSname();
        // member default surname is head's surname
        foreach ($members as $member) {
            $memberSname = $member->getSname();
            if (empty($memberSname)) {
                $member->setSname($sname);
            }
            $em->persist($member);
        }
    }

    public function ageEthnicityDistribution($criteria)
    {
        $parameters = array_merge($criteria['startParameters'], $criteria['startParameters'], ['mArray' => $this->reportMembers($criteria)]);
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $this->createQueryBuilder('m')
                ->distinct()
                ->select('m.id, m.sex, e.ethnicity')
                ->addSelect('YEAR(:startDate) - YEAR(m.dob)-(CASE WHEN DAYOFYEAR(DATE(:startDate)) < DAYOFYEAR(m.dob) THEN 1 ELSE 0 END) age')
                ->join('TruckeeProjectmanaBundle:Ethnicity', 'e', 'WITH', 'm.ethnicity = e')
                ->where('m.id IN (:mArray)')
                ->setParameters($parameters)
                ->getQuery()->getResult()
        ;
    }

    public function uniqueNewMembers($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters'], $criteria['startParameters'], $criteria['startParameters'],
            $criteria['startParameters'], $criteria['startParameters']);
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $this->uniqueMembersQuery($criteria)
                ->setParameters($parameters)
                ->getQuery()->getSingleScalarResult();
        ;
    }

    public function reportMembers($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters'], $criteria['startParameters'], $criteria['startParameters']);
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $this->getEntityManager()->createQueryBuilder()
                ->select('m.id')
                ->from('TruckeeProjectmanaBundle:Member', 'm')
                ->join('TruckeeProjectmanaBundle:Household', 'h', 'WITH', 'm.household = h')
                ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.household = h')
                ->join('TruckeeProjectmanaBundle:Center', 'r', 'WITH', 'c.center = r')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($criteria['contactWhereClause'])
                ->andWhere($qb->expr()->orX('m.excludeDate > :startDate', $qb->expr()->isNull('m.excludeDate')))
                ->andWhere($qb->expr()->orX('m.dob < :startDate', $qb->expr()->isNull('m.dob')))
                ->setParameters($parameters)
                ->getQuery()->getResult();
        ;
    }

    private function uniqueMembersQuery($criteria)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        return $this->createQueryBuilder('m')
                ->select('count(distinct m.id)')
                ->join('TruckeeProjectmanaBundle:Household', 'h', 'WITH', 'm.household = h')
                ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.household = h')
                ->join('TruckeeProjectmanaBundle:Center', 'r', 'WITH', 'c.center = r')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($qb->expr()->orX('m.excludeDate > :startDate', $qb->expr()->isNull('m.excludeDate')))
                ->andWhere('c.first = true')
                ->andWhere($criteria['contactWhereClause']);
    }
}
