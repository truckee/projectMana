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

    public function getTemplateCriteria($criteria)
    {
        return $this->setTemplateCriteria($criteria);
    }

    public function getGeneralCriteria($criteria)
    {
        return $this->setGeneralCriteria($criteria);
    }

    public function getDetailsCriteria($criteria)
    {
        return $this->setDetailsCriteria($criteria);
    }

    private function setDetailsCriteria($criteria)
    {
        $betweenWhereClause = 'c.contactDate BETWEEN :startDate AND :endDate';
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $betweenParameters = ['startDate' => new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear']),
            'endDate' => new \DateTime($criteria['endMonth'] . '/' . $endDay . '/' . $criteria['endYear']),];
        $startWhereClause = 'c.contactDate >= :startDate ';
        $startParameters = ['startDate' => new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear'])];

        return [
            'betweenWhereClause' => $betweenWhereClause,
            'betweenParameters' => $betweenParameters,
            'startWhereClause' => $startWhereClause,
            'startParameters' => $startParameters,
        ];
    }

    private function setGeneralCriteria($criteria)
    {
        $detailsCriteria = $this->setDetailsCriteria($criteria);
        $betweenWhereClause = $detailsCriteria['betweenWhereClause'];
        $betweenParameters = $detailsCriteria['betweenParameters'];
        $startWhereClause = $detailsCriteria['startWhereClause'];
        $startParameters = $detailsCriteria['startParameters'];

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

    private function setTemplateCriteria($criteria)
    {
        $templateCriteria['startDate'] = $criteria['betweenParameters']['startDate'];
        $templateCriteria['endDate'] = $criteria['betweenParameters']['endDate'];
//        $templateCriteria['contactdesc'] = '';
        //check if contactParameters, siteParameters are set to accommodate details report criteria
        if (isset($criteria['contactParameters']) && [] !== $criteria['contactParameters']) {
            $templateCriteria['contactdesc'] = $criteria['contactParameters']['contactdesc']->getContactdesc();
        }
//        $templateCriteria['site'] = '';
        if (isset($criteria['siteParameters']) && [] !== $criteria['siteParameters']) {
            if ('center' === key($criteria['siteParameters'])) {
                $templateCriteria['site'] = $criteria['siteParameters']['center']->getCenter();
            } else {
                $templateCriteria['site'] = $criteria['siteParameters']['county']->getCounty();
            }
        }

        return $templateCriteria;
    }
}
