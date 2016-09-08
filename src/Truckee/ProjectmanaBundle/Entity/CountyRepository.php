<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Truckee\ProjectmanaBundle\Entity\CountyRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CountyRepository.
 */
class CountyRepository extends EntityRepository
{
    /**
     * Get column headers for profile by site reports
     *
     * @param array $dateCriteria
     * @return array
     */
    public function colLabels($dateCriteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT r.county FROM TruckeeProjectmanaBundle:County r '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.county = r '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'ORDER BY r.county')
            ->setParameters($dateCriteria)
            ->getResult();
        $colLabels = [];
        foreach ($qb as $row) {
            $colLabels[] = $row['county'];
        }

        return $colLabels;
    }
}
