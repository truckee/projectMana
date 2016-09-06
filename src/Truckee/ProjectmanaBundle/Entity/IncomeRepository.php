<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\IncomeRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * IncomeRepository.
 */
class IncomeRepository extends EntityRepository
{
    /**
     * Get row labels for profile report
     *
     * @param array $dateCriteria
     * @return array
     */
    public function rowLabels($dateCriteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT i.income FROM TruckeeProjectmanaBundle:Income i '
            . 'JOIN TruckeeProjectmanaBundle:Household h WITH h.income = i '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'AND i.enabled = TRUE ORDER BY i.id')
            ->setParameters($dateCriteria)
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['income'];
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
        $dql = 'SELECT r.__TYPE__ colLabel, i.income rowLabel, COUNT(DISTINCT h.id) N '
            . 'FROM TruckeeProjectmanaBundle:Household h '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ r WITH r = c.__TYPE__ '
            . 'JOIN TruckeeProjectmanaBundle:Income i WITH h.income = i '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'AND i.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($dateCriteria)
            ->getResult();

        return $qb;
    }
}
