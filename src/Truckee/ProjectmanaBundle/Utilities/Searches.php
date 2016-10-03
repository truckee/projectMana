<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Mana/ClientBundle/Utilities/Searches.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Various searches requiring EntityManager.
 */
class Searches
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Set of most recent households with distribution at given site
     *
     * @param ing $site
     *
     * @return array
     */
    public function getLatest($site)
    {
        $em = $this->em;
        $site = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($site);
        $maxDate = $em->createQuery('SELECT MAX(c.contactDate) FROM '
                . 'TruckeeProjectmanaBundle:Contact c WHERE c.center = :site')
            ->setParameter('site', $site)
            ->getSingleScalarResult();
        $contacts = $em->createQuery('SELECT m.sname, m.fname, m.dob, h.id, d.contactDesc FROM TruckeeProjectmanaBundle:Member m '
                . 'JOIN TruckeeProjectmanaBundle:Household h WITH m = h.head '
                . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c.contactDesc = d '
                . 'WHERE c.center = :site AND c.contactDate = :date '
                . 'ORDER BY m.sname, m.fname')
            ->setParameters(['site' => $site, 'date' => $maxDate])
            ->getResult();

        return array(
            'contacts' => $contacts,
            'latestDate' => $maxDate,
        );
    }

    /**
     * Members that match input name.
     *
     * @param string $qtext
     *
     * @return array
     */
    public function getMembers($qtext)
    {
        $string = trim($qtext);
        $length = strpos($qtext, ' ');
        if (empty($length)) {
            return;
        }
        $name = addslashes($string);
        $conn = $this->em->getConnection();
        $sql = "select m.id from member m where match(m.fname, m.sname) against (quote('$name'))";
        $stmt = $conn->query($sql);
        $members = $stmt->fetchAll();
        $found = array();
        foreach ($members as $member) {
            $found[] = $this->em->getRepository('TruckeeProjectmanaBundle:Member')->find($member['id']);
        }

        return $found;
    }

    /**
     * Heads of households with distributions at given site, FY to date
     *
     * @param int $site
     *
     * @return array
     */
    public function getHeadsFYToDate($site)
    {
        $date = new \DateTime();
        $year = date_format($date, 'Y');
        $month = date_format($date, 'n');
        $fy = ($month < 7) ? $year : $year + 1;
        $startDate = $fy - 1 . '-07-01';
        $endDate = $fy . '-06-30';

        $em = $this->em;
        $loc = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($site);
        $contacts = $em->createQuery('SELECT DISTINCT m.sname, m.fname, m.dob, h.id FROM TruckeeProjectmanaBundle:Member m '
                . 'JOIN TruckeeProjectmanaBundle:Household h WITH m = h.head '
                . 'JOIN TruckeeProjectmanaBundle:Contact c WITH c.household = h '
                . 'WHERE c.center = :site AND c.contactDate >= :startDate '
                . 'AND c.contactDate <= :endDate '
                . 'ORDER BY m.sname, m.fname')
            ->setParameters([
                'site' => $loc,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])
            ->getResult();

        return $contacts;
    }

    public function getDisbledOptions($object)
    {
        $methods = get_class_methods($object);
        $values = [];
        foreach ($methods as $method) {
            if ('get' === substr($method, 0, 3) && is_object($object->$method()) && method_exists($object->$method(),
                    'getEnabled') && false === $object->$method()->getEnabled()) {
                $className = $this->get_class_name(get_class($object->$method()));
                $getter = 'get' . $className;
                $values[] = [$className => $object->$method()->$getter()];
            }
        }

        return $values;
    }

    private function get_class_name($classname)
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }

    public function setDisabledOptions($object, $options)
    {
        foreach ($options as $option => $value) {
            $setter = 'set' . $option;
        dump($object);
            $object->$setter($value);
        }

        return $object;
    }
}
