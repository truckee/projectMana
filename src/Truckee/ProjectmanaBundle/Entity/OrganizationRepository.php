<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\OrganizationRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * OrganizationRepository.
 */
class OrganizationRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT o.organization FROM TruckeeProjectmanaBundle:Organization o '
            . 'INNER JOIN o.households h INNER JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'ORDER BY a.id ASC')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['organization'];
        }

        return $rowLabels;
    }

    /**
     * Get profile data
     *
     * @param array $profileType
     *
     * @return array
     */
    public function crossTabData($criteria, $profileType)
    {
        $entity = ucfirst($profileType);
        $dql = 'SELECT ctr.__TYPE__ colLabel, r.organization rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Organization o '
            . 'JOIN r.households h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ ctr WITH ctr = c.__TYPE__ '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'AND o.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }
}
