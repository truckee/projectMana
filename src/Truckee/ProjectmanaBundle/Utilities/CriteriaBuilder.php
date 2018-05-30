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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCriteria($criteria)
    {
        $report = $this->setReportCriteria($criteria);
        $table = $this->setTableCriteria($report);
        $template = $this->setTemplateCriteria($criteria);

        return [
            'report' => $report,
            'table' => $table,
            'template' => $template,
        ];
    }

    private function setReportCriteria($criteria)
    {
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $reportCriteria['startDate'] = new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear']);
        $reportCriteria['endDate'] = new \DateTime($criteria['endMonth'] . '/' . $endDay . '/' . $criteria['endYear']);
        $reportCriteria['contactdesc'] = (!empty($criteria['contactdesc'])) ? $criteria['contactdesc'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';
        $reportCriteria['columnType'] = (!empty($criteria['columnType'])) ? $criteria['columnType'] : '';
        $reportCriteria['contactdesc'] = (!empty($criteria['contactdesc'])) ? $criteria['contactdesc'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

        return $reportCriteria;
    }

    public function getPermanentTableCriteria($criteria)
    {
        return $this->setPermanentTableCriteria($criteria);
    }

    private function setPermanentTableCriteria($criteria)
    {
        $betweenWhereClause = 'c.contactDate BETWEEN :startDate AND :endDate';
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $betweenParameters = ['startDate' => new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear']),
            'endDate' => new \DateTime($criteria['endMonth'] . '/' . $endDay . '/' . $criteria['endYear']),
        ];
        $startWhereClause = 'c.contactDate >= :startDate ';
        $startParameters = ['startDate' => new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear'])];

        $siteWhereClause = $contactWhereClause = '';
        $siteParameters = $contactParameters = [];

        $formSiteCriteria = ['center' => $criteria['center'], 'county' => $criteria['county'],];
        $siteCriteria = $this->setSiteCriteria($formSiteCriteria);

        $formContactCriteria = ['contactdesc' => $criteria['contactdesc']];
        $contactCriteria = $this->setContactCriteria($formContactCriteria);

        return [
            'betweenWhereClause' => $betweenWhereClause,
            'betweenParameters' => $betweenParameters,
            'startWhereClause' => $startWhereClause,
            'startParameters' => $startParameters,
            'siteWhereClause' => $siteCriteria['siteWhereClause'],
            'siteParameters' => $siteCriteria['siteParameters'],
            'contactWhereClause' => $contactCriteria['contactWhereClause'],
            'contactParameters' => $contactCriteria['contactParameters'],
        ];
    }

    private function setSiteCriteria($formSiteCriteria)
    {
        if ('' !== $formSiteCriteria['center']) {
            $id = $formSiteCriteria['center'];
            $center = $this->em->getRepository('TruckeeProjectmanaBundle:Center')->find($id);
            $siteWhereClause = 'c.center = :center';
            $siteParameters = ['center' => $center];
        } elseif ('' !== $formSiteCriteria['county']) {
            $id = $formSiteCriteria['county'];
            $county = $this->em->getRepository('TruckeeProjectmanaBundle:County')->find($id);
            $siteWhereClause = 'c.county = :county';
            $siteParameters = ['county' => $county];
        } else {
            $siteWhereClause = '1 = 1';
            $siteParameters = [];
        }

        return [
            'siteWhereClause' => $siteWhereClause,
            'siteParameters' => $siteParameters,
        ];
    }

    private function setContactCriteria($formSiteCriteria)
    {
        if ('' !== $formSiteCriteria['contactdesc']) {
            $id = $formSiteCriteria['contactdesc'];
            $contactdesc = $this->em->getRepository('TruckeeProjectmanaBundle:Contactdesc')->find($id);
            $contactWhereClause = 'c.contactdesc = :contactdesc';
            $contactParameters = ['contactdesc' => $contactdesc];
        } else {
            $contactWhereClause = '1 = 1';
            $contactParameters = [];
        }

        return [
            'contactWhereClause' => $contactWhereClause,
            'contactParameters' => $contactParameters,
        ];
    }

    private function setTableCriteria($criteria)
    {
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
        if (!empty($criteria['contactdesc'])) {
            $newWhere .= ' and c.contactdesc = :contactdesc';
            $tableWhere .= ' and c.contactdesc_id  = :contactdesc';
            $parameters['contactdesc'] = $criteria['contactdesc'];
        }

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
        $templateCriteria['startDate'] = new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear']);
        $templateCriteria['endDate'] = new \DateTime($criteria['endMonth'] . '/' . $endDay . '/' . $criteria['endYear']);

        $templateCriteria['contactdesc'] = (!empty($criteria['contactdesc'])) ? $criteria['contactdesc'] : '';
        $templateCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $templateCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

        $em = $this->em;
        if (!empty($templateCriteria['contactdesc'])) {
            $typeObj = $em->getRepository('TruckeeProjectmanaBundle:Contactdesc')->find($templateCriteria['contactdesc']);
            $templateCriteria['contactdesc'] = $typeObj->getContactdesc();
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
