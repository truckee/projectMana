<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Services\Experiment.php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Crosstab.
 *
 * @author George Brooks
 */
class Crosstab
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Organize row and column data for profile report
     *
     * @param array $data      = data array
     * @param array $rowLabels
     * @param array $colLabels
     *
     * @return array
     */
    public function crosstabQuery($data, $rowLabels, $colLabels)
    {
        // initialize profile
        $profile = $this->profileArray($rowLabels, $colLabels);
        // calculate totals
        foreach ($data as $array) {
            $profile[$array['rowLabel']][$array['colLabel']] = $array['N'];
            $profile[$array['rowLabel']]['total'] += $array['N'];
            $profile['total'][$array['colLabel']] += $array['N'];
            $profile['total']['total'] += $array['N'];
        }

        return $profile;
    }

    public function crosstabData($criteria, $profileParameters)
    {
        $profileType = $criteria['columnType'];
        $entityField = $profileParameters['entityField'];
        $joinField = $profileParameters['joinField'];

        return $this->em->createQueryBuilder()
                ->select('r.' . $profileType . ' colLabel, alias.' . $entityField . ' rowLabel, COUNT(DISTINCT h.id) N ')
                ->from('App:Household', 'h')
                ->join('h.contacts', 'c')
                ->join('c.' . $profileType, 'r')
                ->join('h.' . $joinField, 'alias')
                ->andWhere('alias.enabled = TRUE')
                ->groupBy('colLabel, rowLabel')
                ->where($criteria['betweenWhereClause'])
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
    }

    public function mtmCrosstabData($criteria, $profileParameters)
    {
        $profileType = $criteria['columnType'];
        $entity = $profileParameters['entity'];
        $entityField = $profileParameters['entityField'];
        return $this->em->createQueryBuilder()
                ->select('r.' . $profileType . ' colLabel, alias.' . $entityField . ' rowLabel, COUNT(DISTINCT h.id) N ')
                ->from('App:' . ucfirst($entity), 'alias')
                ->join('alias.households', 'h')
                ->join('h.contacts', 'c')
                ->join('c.' . $profileType, 'r')
                ->where($criteria['betweenWhereClause'])
                ->groupBy('colLabel, rowLabel')
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
    }

    //for many to one relationships: household to entity
    public function rowLabels($criteria, $profileParameters)
    {
        $entityField = $profileParameters['entityField'];
        $joinField = $profileParameters['joinField'];
        $qb = $this->em->createQueryBuilder()
                ->select('alias.' . $entityField)
                ->distinct()
                ->from('App:Household', 'h')
                ->join('h.' . $joinField, 'alias')
                ->join('h.contacts', 'c')
                ->where($criteria['betweenWhereClause'])
                ->orderBy('alias.' . $entityField)
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row[$entityField];
        }

        return $rowLabels;
    }

    public function colLabels($criteria)
    {
        $profileType = $criteria['columnType'];
        $entity = ucfirst($profileType);
        $query = $this->em->createQueryBuilder()
                ->select('r.' . $profileType)
                ->from('App:' . $entity, 'r')
                ->distinct()
                ->join('r.contacts', 'c')
                ->where($criteria['betweenWhereClause'])
                ->orderBy('r.' . $profileType)
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
        $colLabels = [];
        foreach ($query as $row) {
            $colLabels[] = $row[$profileType];
        }

        return $colLabels;
    }

    //row label function for many-to-many relationship: household to entity
    public function mtmRowLabels($criteria, $profileParameters)
    {
        $entity = $profileParameters['entity'];
        $entityField = $profileParameters['entityField'];
        $qb = $this->em->createQueryBuilder()
                ->select('alias.' . $entityField)
                ->distinct()
                ->from('App:' . ucfirst($entity), 'alias')
                ->join('alias.households', 'h')
                ->join('h.contacts', 'c')
                ->where($criteria['betweenWhereClause'])
                ->orderBy('alias.' . $entityField)
                ->setParameters($criteria['betweenParameters'])
                ->getQuery()->getResult()
        ;
        $rowLabels = [];
        foreach ($qb as $row) {
            $rowLabels[] = $row[$entityField];
        }

        return $rowLabels;
    }

    /**
     * Initialize row, column, total entries to 0
     *
     * @param array $rows
     * @param array $cols
     *
     * @return array
     */
    private function profileArray($rows, $cols)
    {
        $colKeys = [];
        $profile = [];
        foreach ($cols as $col) {
            $colKeys[$col] = 0;
            $profile['total'][$col] = 0;
        }
        $colKeys['total'] = 0;
        $profile['total']['total'] = 0;
        foreach ($rows as $row) {
            $profile[$row] = $colKeys;
        }

        return $profile;
    }
}
