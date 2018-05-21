<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\CriteriaBuilder.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Criteria
 *
 * @author George
 */
class CriteriaBuilder
{
    private $em;
    private $reportCriteria;
    private $tableCriteria;
    private $templateCriteria;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setCriteria($criteria)
    {
        $this->reportCriteria = $this->setReportCriteria($criteria);
        $this->tableCriteria = $this->setTableCriteria($criteria);
        $this->templateCriteria = $this->setTemplateCriteria($criteria);
    }

    public function getReportCriteria()
    {
        return $this->reportCriteria;
    }

    public function getTableCriteria()
    {
        return $this->tableCriteria;
    }

    public function getTemplateCriteria()
    {
        return $this->templateCriteria;
    }

    private function setReportCriteria($criteria)
    {
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $reportCriteria['startDate'] = new \DateTime($criteria['startMonth'].'/01/'.$criteria['startYear']);
        $reportCriteria['endDate'] = new \DateTime($criteria['endMonth'].'/'.$endDay.'/'.$criteria['endYear']);
        $reportCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';
        $reportCriteria['columnType'] = (!empty($criteria['columnType'])) ? $criteria['columnType'] : '';
        $reportCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

        return $reportCriteria;
    }

    private function setTableCriteria()
    {
        $criteria = $this->getReportCriteria();
        //string dates required
        $parameters = [
            'startDate' => date_format($criteria['startDate'], 'Y-m-d'),
            'endDate' => date_format($criteria['endDate'], 'Y-m-d'),
            ];
        $incoming = array(
            'county' => (!empty($criteria['county'])) ? $criteria['county'] : '',
            'center' => (!empty($criteria['center'])) ? $criteria['center'] : '',
        );
        // doctrine queries
        $newWhere = ' WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate';
        // dbal queries
        $tableWhere = ' WHERE c.contact_date >= :startDate AND c.contact_date <= :endDate';

        $options = array('county', 'center');
        foreach ($options as $opt) {
            if ('' !== $incoming[$opt]) {
                $newWhere .= " and c.$opt = :$opt";
                $tableWhere .= ' and c.' . $opt . '_id = :' . $opt;
                $parameters[$opt] = $incoming[$opt];
            }
        }
        if (!empty($criteria['contact_type'])) {
            $newWhere .= ' and c.contactType = :contactType';
            $tableWhere .= ' and c.contact_type_id  = :contactType';
            $parameters['contactType'] = $criteria['contact_type'];
        }
        //set criteria for common statistics
        return [
            'newWhereClause' => $newWhere,
            'tableWhereClause' => $tableWhere,
            'parameters' => $parameters,
        ];
    }

    private function setTemplateCriteria($criteria)
    {
        // get specs to pass to template
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $templateCriteria['startDate'] = new \DateTime($criteria['startMonth'].'/01/'.$criteria['startYear']);
        $templateCriteria['endDate'] = new \DateTime($criteria['endMonth'].'/'.$endDay.'/'.$criteria['endYear']);

        $templateCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $templateCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $templateCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

        $em = $this->em;
        if (!empty($templateCriteria['contact_type'])) {
            $typeObj = $em->getRepository('TruckeeProjectmanaBundle:ContactDesc')->find($templateCriteria['contact_type']);
            $templateCriteria['contact_type'] = $typeObj->getContactDesc();
        }

        if (!empty($templateCriteria['center'])) {
            $centerObj = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($templateCriteria['center']);
            $templateCriteria['center'] = $centerObj->getCenter();
        }

        if (!empty($templateCriteria['county'])) {
            $countyObj = $em->getRepository('TruckeeProjectmanaBundle:County')->find($templateCriteria['county']);
            $templateCriteria['county'] = $countyObj->getCounty();
        }

        return $templateCriteria;
    }
}
