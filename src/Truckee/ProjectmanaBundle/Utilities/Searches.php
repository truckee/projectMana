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
 * returns set of households whose heads are possible matches for new head.
 */
class Searches
{

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getLatest($site) {
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
     * get members that match input name.
     *
     * @param type $qtext
     *
     * @return array
     */
    public function getMembers($qtext) {
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

}
