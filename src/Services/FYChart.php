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
class FYChart
{
    use \App\Services\FYFunction;

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
        $fy = $this->fy();
        $month = date_format(new \DateTime(), 'n');
        $chart['fy'] = $fy;
        $fy_months = array_merge(range(7, 12), range(1, 6));
        $i = 0;
        $categories = [];
        for ($i = 0; $fy_months[$i] != $month; ++$i) {
            $categories[] = date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F');
//            $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "', ";
        }
        $categories[] = date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F');
//        $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "'";

        $siteQuery = $this->em->createQueryBuilder()
                ->select('r.center')
                ->from('App:Center', 'r')
                ->where('r.enabled = TRUE')
                ->orderBy('r.center')
                ->getQuery()
                ->getResult();

//        $series = '[';
        $allSeries = [];
        $dataSeries = [];
        foreach ($siteQuery as $center) {
            $site = $center['center'];
            $dataSeries['name'] = $site;
            $dataSeries['data'] = [];
            $qb = $this->em->createQueryBuilder()
                            ->select('(CASE WHEN MONTH(c.contactDate) > 6 THEN MONTH(c.contactDate) - 7 ELSE MONTH(c.contactDate) + 6 END) Mo, COUNT(DISTINCT c.household) N')
                            ->from('App:Contact', 'c')
                            ->join('App:Center', 'r', 'WITH', 'c.center = r')
                            ->where('r.center = :site')
                            ->andWhere('(CASE WHEN MONTH(c.contactDate)<7 THEN YEAR(c.contactDate) ELSE YEAR(c.contactDate) + 1 END) = :fy')
                            ->setParameters(['site' => $site, 'fy' => $fy])
                            ->groupBy('Mo')
                            ->orderBy('Mo')
                            ->getQuery()->getResult();

            foreach ($qb as $array) {
                $dataSeries['data'][] = (int) $array['N'];
            }
            $allSeries[] = $dataSeries;
        }

        $ob = new Highchart();
        $ob->chart->type('column');
        $ob->chart->renderTo('linechart');
        $ob->title->text('Distributions FY' . $fy . ' to date');
        $ob->yAxis->title(array('text' => "Distributions"));
        $ob->xAxis->title(array('text' => "Months"));
        $ob->xAxis->categories($categories);
        $ob->series($allSeries);

        return $ob;
    }
}
