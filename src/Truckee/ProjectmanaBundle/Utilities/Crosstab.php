<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\Experiment.php

namespace Truckee\ProjectmanaBundle\Utilities;

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
        $profile = $this->profileArray($rowLabels, $colLabels);
        foreach ($data as $array) {
            if (!array_key_exists('total', $profile[$array['rowLabel']])) {
                $profile[$array['rowLabel']]['total'] = 0;
            }
            $profile[$array['rowLabel']][$array['colLabel']] = $array['N'];
            $profile[$array['rowLabel']]['total'] += $array['N'];
        }
        foreach ($profile as $key => $array) {
            if (!array_key_exists('total', $array)) {
                $profile[$key]['total'] = 0;
            }
        }
        $profile['total'] = [];
        foreach ($profile as $row => $array) {
            foreach ($array as $key => $value) {
                if (!array_key_exists($key, $profile['total'])) {
                    $profile['total'][$key] = 0;
                }
                $profile['total'][$key] += $value;
            }
        }

        return $profile;
    }

    public function crosstabData($criteria, $profileParameters)
    {
        $profileType = $criteria['columnType'];
        $entity = $profileParameters['entity'];
        $entityField = $profileParameters['entityField'];
        $joinField = $profileParameters['joinField'];

        return $this->em->createQueryBuilder()
                ->select('r.' . $profileType . ' colLabel, alias.' . $entityField . ' rowLabel, COUNT(DISTINCT h.id) N ')
                ->from('TruckeeProjectmanaBundle:Household', 'h')
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
                ->from('TruckeeProjectmanaBundle:' . ucfirst($entity), 'alias')
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
        $entity = $profileParameters['entity'];
        $entityField = $profileParameters['entityField'];
        $joinField = $profileParameters['joinField'];
        $qb = $this->em->createQueryBuilder()
                ->select('alias.' . $entityField)
                ->distinct()
                ->from('TruckeeProjectmanaBundle:Household', 'h')
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
                ->from('TruckeeProjectmanaBundle:' . $entity, 'r')
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
                ->from('TruckeeProjectmanaBundle:' . ucfirst($entity), 'alias')
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
     * Initialize row, column entries to 0
     *
     * @param array $rows
     * @param array $cols
     *
     * @return array
     */
    private function profileArray($rows, $cols)
    {
        $colKeys = [];
        foreach ($cols as $col) {
            $colKeys[$col] = 0;
        }
        $profile = [];
        foreach ($rows as $row) {
            $profile[$row] = $colKeys;
        }

        return $profile;
    }
}
