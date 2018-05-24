<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\FYChart

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Project MANA statistics.
 */
class FYChart
{
    use \Truckee\ProjectmanaBundle\Utilities\FYFunction;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get site distributions by month for index page chart
     *
     * @return string
     */
    public function getDistsFYToDate()
    {
//        $fiscalYear = new FYFunction();
        $fy = $this->fy();
        $month = date_format(new \DateTime(), 'n');
        $chart['fy'] = $fy;
        $fy_months = array_merge(range(7, 12), range(1, 6));
        $i = 0;
        for ($i = 0; $fy_months[$i] != $month; ++$i) {
            $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "', ";
        }
        $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "'";

        $siteQuery = $this->em->createQueryBuilder()
            ->select('r.center')
            ->from('TruckeeProjectmanaBundle:Center', 'r')
            ->where('r.enabled = TRUE')
            ->orderBy('r.center')
            ->getQuery()
            ->getResult();

        $series = '[';
        foreach ($siteQuery as $center) {
            $site = $center['center'];
            $seriesString = "{name:'$site', data:[";
            $qb = $this->em->createQueryBuilder()
                ->select('(CASE WHEN MONTH(c.contactDate) >= 6 THEN MONTH(c.contactDate) - 6 ELSE MONTH(c.contactDate) + 6 END) Mo, COUNT(DISTINCT c.household) N')
                ->from('TruckeeProjectmanaBundle:Contact', 'c')
                ->join('TruckeeProjectmanaBundle:Center', 'r', 'WITH', 'c.center = r')
                ->where('r.center = :site')
                ->andWhere('(CASE WHEN MONTH(c.contactDate)<7 THEN YEAR(c.contactDate) ELSE YEAR(c.contactDate) + 1 END) = :fy')
                ->setParameters(['site' => $site, 'fy' => $fy])
                ->groupBy('Mo')
                ->orderBy('Mo')
                ->getQuery()->getResult();
          
            foreach ($qb as $array) {
                $seriesString .= $array['N'] . ',';
            }
            $series .= $seriesString . ']}, ';
        }
        $chart['series'] = $series . ']';

        return $chart;
    }

    private function getFY()
    {
        $year = date_format(new \DateTime(), 'Y');
        $month = date_format(new \DateTime(), 'n');
        $fy = ($month < 7) ? $year : $year + 1;

        return $fy;
    }
}
