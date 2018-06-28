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

class HouseholdRepository extends EntityRepository
{

    /**
     * Add contact to set of households
     *
     * @param array $households
     * @param array $contactData
     */
    public function addContacts($households, $contactData)
    {
        $em = $this->getEntityManager();
        foreach ($households as $id) {
            $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
            $houseContacts = $household->getContacts();
            $nContacts = count($houseContacts);
            $first = ($nContacts > 0) ? 0 : 1;
            $contact = new Contact();
            $contact->setContactDate($contactData['date']);
            $contact->setCenter($contactData['center']);
            $center = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($contactData['center']);
            $county = $em->getRepository('TruckeeProjectmanaBundle:County')->find($center->getCounty());
            $contact->setCounty($county);
            $contact->setContactdesc($contactData['desc']);
            $contact->setFirst($first);
            $household->addContact($contact);
            $em->persist($household);
        }
        $em->flush();
    }

    /**
     * Annual turkey report
     */
    public function annualTurkey()
    {
        $jan1 = new \DateTime('first day of January');
        $jul1 = new \DateTime('first day of July');

        return $this->getEntityManager()->createQueryBuilder()
                ->select('m.sname', 'm.fname', 'm.dob', 'm.id', 'r.center', 'CASE WHEN MAX(c.contactDate) <= :jul1 THEN \'Yes\' ELSE \'No\' END Form')
                ->distinct(true)
                ->from('TruckeeProjectmanaBundle:Household', 'h')
                ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.household = h')
                ->join('TruckeeProjectmanaBundle:Center', 'r', 'WITH', 'c.center = r')
                ->join('TruckeeProjectmanaBundle:Member', 'm', 'WITH', 'h.head = m')
                ->where('c.contactDate >= :jan1')
                ->groupBy('m.sname')
                ->addGroupBy('m.fname')
                ->orderBy('m.sname')
                ->addOrderBy('m.fname')
                ->setParameter('jan1', $jan1)
                ->setParameter('jul1', $jul1)
                ->getQuery()
                ->getResult();
    }

    public function size($criteria)
    {
        $parameters = array_merge($criteria['startParameters'], $criteria['startParameters'], ['hArray' => $this->reportHousehold($criteria)]);
        $qb = $this->getEntityManager()->createQueryBuilder();

        $sizeData = $this->getEntityManager()->createQueryBuilder()
                ->select('distinct h.id, count(m.id) size')
                ->from('TruckeeProjectmanaBundle:Member', 'm')
                ->join('m.household', 'h')
                ->where('h.id IN (:hArray)')
                ->andWhere($qb->expr()->orX('m.excludeDate > :startDate', $qb->expr()->isNull('m.excludeDate')))
                ->andWhere($qb->expr()->orX('m.dob < :startDate', $qb->expr()->isNull('m.dob')))
                ->groupBy('h.id')
                ->setParameters($parameters)
                ->getQuery()->getResult();

        return $sizeData;
    }

    public function reportHousehold($criteria)
    {
        $parameters = array_merge($criteria['betweenParameters'], $criteria['siteParameters'], $criteria['contactParameters']);

        return $this->createQueryBuilder('i')
                ->select('i.id')
                ->join('TruckeeProjectmanaBundle:Contact', 'c', 'WITH', 'c.household = i')
                ->where($criteria['betweenWhereClause'])
                ->andWhere($criteria['siteWhereClause'])
                ->andWhere($criteria['contactWhereClause'])
                ->setParameters($parameters)
                ->getQuery()->getResult()
        ;
    }
}
