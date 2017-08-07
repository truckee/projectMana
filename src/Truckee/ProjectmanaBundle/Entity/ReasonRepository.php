<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\ReasonRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ReasonRepository.
 */
class ReasonRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($dateCriteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT r.reason FROM TruckeeProjectmanaBundle:Reason r '
            . 'INNER JOIN r.households h INNER JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'ORDER BY r.id ASC')
            ->setParameters($dateCriteria)
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['reason'];
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
    public function crossTabData($dateCriteria, $profileType)
    {
        $entity = ucfirst($profileType);
        $dql = 'SELECT ctr.__TYPE__ colLabel, r.reason rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Reason r '
            . 'JOIN r.households h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ ctr WITH ctr = c.__TYPE__ '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'AND r.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($dateCriteria)
            ->getResult();

        return $qb;
    }
}
