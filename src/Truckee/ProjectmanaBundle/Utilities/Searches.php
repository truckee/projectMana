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

    /**
     * Create array of disabled fields of an entity object
     * 
     * @param type $object
     * @return array
     */
    public function getDisabledOptions($object) {
        $values = [];
        $className = get_class($object);
        $metaData = $this->em->getClassMetadata($className);
        foreach ($metaData->associationMappings as $field => $mapping) {
            if (8 > $mapping['type']) {
                $fieldName = ucfirst($field);
                $method = 'get' . $fieldName;
                if (method_exists($object->$method(), 'getEnabled') && false === $object->$method()->getEnabled()) {
                    $values[] = $fieldName;
                }
            }
        }
        $manyToMany = json_decode($this->getMetaData($object), true);
        foreach(array_keys($manyToMany) as $key) {
            $values[] = $key;
        }

        return $values;
    }

    /**
     * Get array of disabled ManyToMany options
     *
     * @param Object $object
     * @return array
     */
    public function getMetaData($object) {
        $data = array();
        $className = get_class($object);
        $metaData = $this->em->getClassMetadata($className);
        foreach ($metaData->associationMappings as $field => $mapping) {
            if (8 === $mapping['type']) {
                $data[$field] = $this->extractOptions($object, $field);
            }
        }

        return json_encode($data);
    }

    private function extractOptions($object, $field) {
        $data = [];
        $method = 'get' . ucfirst($field);
        $itemName = substr($field, 0, -1);
        $getter = 'get' . ucfirst($itemName);
        $entity = $object->$method();
        foreach ($entity as $item) {
            if (method_exists($item, 'getEnabled') && false === $item->getEnabled()) {
                $data[] = ['id' => $item->getId()];
            }
        }

        return $data;
    }
}
