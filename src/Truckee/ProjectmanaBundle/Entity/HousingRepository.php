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
 * Description of HousingRepository
 *
 * @author George
 */
class HousingRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT hs.housing FROM TruckeeProjectmanaBundle:Housing hs '
            . 'JOIN TruckeeProjectmanaBundle:Household h WITH h.housing = hs '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'ORDER BY hs.housing ASC')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['housing'];
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
        $dql = 'SELECT r.__TYPE__ colLabel, hs.housing rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Household h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ r WITH r = c.__TYPE__ '
            . 'JOIN TruckeeProjectmanaBundle:Housing hs WITH h.housing = hs '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'AND hs.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($criteria['betweenParameters'])
            ->getResult();

        return $qb;
    }
}
