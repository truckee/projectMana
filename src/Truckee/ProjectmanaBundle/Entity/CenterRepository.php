<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\CenterRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CenterRepository.
 *
 * @author George Brooks
 */
class CenterRepository extends EntityRepository
{
    /**
     * Get column headers for profile by site reports
     *
     * @param array $dateCriteria
     * @return array
     */
    public function colLabels($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT r.center FROM TruckeeProjectmanaBundle:Center r '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.center = r '
            . 'WHERE c.contactDate between :startDate AND :endDate '
            . 'ORDER BY r.center')
            ->setParameters($criteria['betweenParameters'])
            ->getResult();
        $colLabels = [];
        foreach ($qb as $row) {
            $colLabels[] = $row['center'];
        }

        return $colLabels;
    }
}
