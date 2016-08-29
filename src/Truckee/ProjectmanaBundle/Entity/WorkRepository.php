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
    public function rowLabels($dateCriteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT w.work FROM TruckeeProjectmanaBundle:Work w '
            . 'JOIN TruckeeProjectmanaBundle:Member m WITH m.work = w '
            . 'JOIN TruckeeProjectmanaBundle:Household h WITH h.head = m '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = m.household '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'AND w.enabled = TRUE ORDER BY w.work')
            ->setParameters($dateCriteria)
            ->getResult();
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row['work'];
        }

        return $rowLabels;
    }

    public function crossTabData($dateCriteria, $profileType)
    {
        $entity = ucfirst($profileType);
        $dql = 'SELECT r.__TYPE__ colLabel, w.work rowLabel, COUNT(DISTINCT m.id) N '
            . 'FROM TruckeeProjectmanaBundle:Work w '
            . 'JOIN TruckeeProjectmanaBundle:Member m WITH m.work = w '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = m.household '
            . 'JOIN TruckeeProjectmanaBundle:__ENTITY__ r WITH r = c.__TYPE__ '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'AND w.enabled = TRUE  GROUP BY colLabel, rowLabel';
        $dql = str_replace('__TYPE__', $profileType, $dql);
        $dql = str_replace('__ENTITY__', $entity, $dql);
        $qb = $this->getEntityManager()->createQuery($dql)
            ->setParameters($dateCriteria)
            ->getResult();

        return $qb;
    }
}
