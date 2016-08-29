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
 * @author George
 */
class CenterRepository extends EntityRepository
{
    public function colLabels($dateCriteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT r.center FROM TruckeeProjectmanaBundle:Center r '
            . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.center = r '
            . 'WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate '
            . 'ORDER BY r.center')
            ->setParameters($dateCriteria)
            ->getResult();
        $colLabels = [];
        foreach ($qb as $row) {
            $colLabels[] = $row['center'];
        }

        return $colLabels;
    }
}
