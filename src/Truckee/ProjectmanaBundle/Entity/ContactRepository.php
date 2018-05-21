<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\ContactRepository.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ContactRepository
 *
 * @author George
 */
class ContactRepository extends EntityRepository
{

    /**
     * Get set of households with multiple same-date contacts
     *
     * @param type $criteria
     *
     * @return array
     */
    public function getMultiContacts($criteria)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT DISTINCT IDENTITY(c1.household) id, m.fname, m.sname, '
                . 'r.center, c1.contactDate, d.contactDesc FROM TruckeeProjectmanaBundle:Contact c1 '
                . 'JOIN TruckeeProjectmanaBundle:Contact c2 WITH c1.household = c2.household '
                . 'JOIN TruckeeProjectmanaBundle:Household h WITH c1.household = h '
                . 'JOIN TruckeeProjectmanaBundle:Member m WITH m = h.head '
                . 'JOIN TruckeeProjectmanaBundle:Center r WITH c1.center = r '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c1.contactDesc = d '
                . 'WHERE c1.contactDate = c2.contactDate '
                . 'AND c1 <> c2 '
                . 'AND c1.contactDate >= :startDate AND c1.contactDate <= :endDate')
            ->setParameters(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']])
            ->getResult();

        return $qb;
    }
}