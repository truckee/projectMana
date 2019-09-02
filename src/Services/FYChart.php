<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Services\FYChart

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Project MANA statistics.
 */
class FYChart {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * Get previous 12 months of site distributions by month for index page chart
     */
    public function getPreviousDists() {
        $m = date_format(new \DateTime(), 'm');
        $y = date_format(new \DateTime(), 'Y');

        $siteQuery = $this->em->createQueryBuilder()
                ->select('r.center')
                ->from('App:Center', 'r')
                ->where('r.enabled = TRUE')
                ->orderBy('r.center')
                ->getQuery()
                ->getResult();
        $allSeries = [];
        $dataSeries = [];

        foreach ($siteQuery as $center) {
            $site = $center['center'];
            $dataSeries['name'] = $site;
            $dataSeries['data'] = [];
            for ($i = 0; $i < 12; $i++) {
                // $pm = previous month as integer, $py = year
                $pm = ($m + $i <= 12) ? $m + $i : $m + $i - 12;
                $py = ($pm >= $m) ? $y - 1 : $y;
                $categories[] = ($m + $i <= 12) ? date("F", mktime(0, 0, 0, $m + $i, 10)) : date("F", mktime(0, 0, 0, $m + $i - 12, 10));
                $qb = $this->em->createQueryBuilder()
                                ->select('COUNT(DISTINCT c.household) N')
                                ->from('App:Contact', 'c')
                                ->join('App:Center', 'r', 'WITH', 'c.center = r')
                                ->where('r.center = :site')
                                ->andWhere('MONTH(c.contactDate) = :month')
                                ->andWhere('YEAR(c.contactDate) = :year')
                                ->setParameters(['site' => $site,'month' => $pm, 'year' => $py])
                                ->getQuery()->getSingleResult();
                $dataSeries['data'][] = (int) $qb['N'];
            }
            $allSeries[] = $dataSeries;
        }
        $ob = new Highchart();
        $ob->chart->type('line');
        $ob->chart->renderTo('linechart');
        $ob->title->text('Previous 12 Months Distributions');
        $ob->yAxis->title(array('text' => "Distributions"));
        $ob->xAxis->title(array('text' => "Months"));
        $ob->xAxis->categories($categories);
        $ob->series($allSeries);

        return $ob;
        }

}
