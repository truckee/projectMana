<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\AssistanceRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AssistanceRepository.
 */
class AssistanceRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT a.assistance FROM TruckeeProjectmanaBundle:Assistance a '
            . 'INNER JOIN a.households h INNER JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'ORDER BY a.id ASC')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['assistance'];
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
        $dql = 'SELECT ctr.__TYPE__ colLabel, r.assistance rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Assistance a '
            . 'JOIN r.households h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ ctr WITH ctr = c.__TYPE__ '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'AND a.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }
}
