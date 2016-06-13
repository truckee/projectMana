<?php

//src/Mana/ClientBundle/Utilities/Searches.php

namespace Mana\ClientBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * returns set of households whose heads are possible matches for new head
 */
class Searches {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getLatest($site)
    {
        $em = $this->em;
        $site = $em->getRepository('ManaClientBundle:Center')->find($site);
        $maxDate = $em->createQuery('SELECT MAX(c.contactDate) FROM '
                . 'ManaClientBundle:Contact c WHERE c.center = :site')
                ->setParameter('site', $site)
                ->getSingleScalarResult();
        $contacts = $em->createQuery('SELECT c FROM ManaClientBundle:Contact c '
                . 'JOIN ManaClientBundle:Household h WITH c.household = h '
                . 'JOIN ManaClientBundle:Member m WITH h.head = m '
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
     * get members that match input name
     * @param type $qtext
     * @return array
     */
    public function getMembers($qtext) {
        $string = trim($qtext);
        $length = strpos($qtext, ' ');
        if (empty($length)) {
            return null;
        }
        $name = addslashes($string);
        $conn = $this->em->getConnection();
        $sql = "select m.id from member m where match(m.fname, m.sname) against (quote('$name'))";
        $stmt = $conn->query($sql);
        $members = $stmt->fetchAll();
        $found = array();
        foreach ($members as $member) {
            $found[] = $this->em->getRepository('ManaClientBundle:Member')->find($member['id']);
        }
        
        return $found;
    }

}
