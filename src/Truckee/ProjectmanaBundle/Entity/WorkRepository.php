<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\WorkRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WorkRepository.
 */
class WorkRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT w.work FROM TruckeeProjectmanaBundle:Work w '
            . 'JOIN TruckeeProjectmanaBundle:Member m WITH m.work = w '
            . 'JOIN TruckeeProjectmanaBundle:Household h WITH h.head = m '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = m.household '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'ORDER BY w.work')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['work'];
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
        $dql = 'SELECT r.__TYPE__ colLabel, w.work rowLabel, COUNT(DISTINCT m.id) N '
            . 'FROM TruckeeProjectmanaBundle:Work w '
            . 'JOIN TruckeeProjectmanaBundle:Member m WITH m.work = w '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = m.household '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ r WITH r = c.__TYPE__ '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'AND w.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }
}
