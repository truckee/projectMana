<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\NotfoodstampRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotfoodstampRepository.
 */
class NotfoodstampRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT n.notfoodstamp FROM TruckeeProjectmanaBundle:Household h '
                . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
                . 'JOIN TruckeeProjectmanaBundle:Notfoodstamp n WITH h.notfoodstamp = n '
                . 'WHERE c.contactDate between :startDate AND :endDate ')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['notfoodstamp'];
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
    public function crossTabData($criteria, $profileType)
    {
        $entity = ucfirst($profileType);
        $dql = 'SELECT r.__TYPE__ colLabel, n.notfoodstamp rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Household h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ r WITH r = c.__TYPE__ '
            . 'JOIN TruckeeProjectmanaBundle:Notfoodstamp n WITH h.notfoodstamp = n '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'AND n.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }
}
