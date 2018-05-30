<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\DetailsReport.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DetailsReport
 *
 * @author George
 */
class DetailsReport
{
    private $em;
    private $detailStatistics;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDetailStatistics()
    {
        $stats = $this->setDetailStatistics();

        return $stats;
    }

    private function setDetailStatistics()
    {
        $countyDescQuery = $this->em->createQuery('SELECT DISTINCT cty.county, d.contactdesc FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:Contactdesc d WITH c.contactdesc = d '
                . 'ORDER BY cty.county, d.contactdesc')->getResult();

        $countystats = [];
        foreach ($countyDescQuery as $countyDesc) {
            $county = $countyDesc['county'];
            $desc = $countyDesc['contactdesc'];
            $countystats[$county][$desc]['uniqInd'] = 0;
            $countystats[$county][$desc]['uniqHouse'] = 0;
            $countystats[$county][$desc]['totalInd'] = 0;
            $countystats[$county][$desc]['totalHouse'] = 0;
        }

        $householdSizeQuery = $this->em->createQuery('SELECT h.id, h.size FROM TruckeeProjectmanaBundle:TempHousehold h')->getResult();
        foreach ($householdSizeQuery as $row) {
            $householdSizeArray[$row['id']] = $row['size'];
        }
        $distinctCountyDescIndividualQuery = $this->em->createQuery('SELECT DISTINCT cty.county, d.contactdesc, c.household FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:Contactdesc d WITH c.contactdesc = d')->getResult();
        $countyDescIndividualQuery = $this->em->createQuery('SELECT cty.county, d.contactdesc, c.household FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:Contactdesc d WITH c.contactdesc = d')->getResult();
        foreach ($distinctCountyDescIndividualQuery as $distinctCountyDescIndividual) {
            $houshold = $distinctCountyDescIndividual['household'];
            $size = $householdSizeArray[$houshold];
            $cty = $distinctCountyDescIndividual['county'];
            $cDesc = $distinctCountyDescIndividual['contactdesc'];
            $countystats[$cty][$cDesc]['uniqInd'] += $size;
            $countystats[$cty][$cDesc]['uniqHouse'] ++;
        }
        foreach ($countyDescIndividualQuery as $countyDescIndividual) {
            $houshold = $countyDescIndividual['household'];
            $size = $householdSizeArray[$houshold];
            $cty = $countyDescIndividual['county'];
            $cDesc = $countyDescIndividual['contactdesc'];
            $countystats[$cty][$cDesc]['totalInd'] += $size;
            $countystats[$cty][$cDesc]['totalHouse'] ++;
        }
        $details = array(
            'details' => $countystats,
        );

        return $details;
    }
}
