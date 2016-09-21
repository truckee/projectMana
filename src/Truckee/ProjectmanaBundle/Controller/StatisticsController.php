<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Truckee\ProjectmanaBundle\Form\ReportCriteriaType;

/**
 * Present various Project MANA statistics.
 *
 * @Route("/reports")
 */
class StatisticsController extends Controller
{
    /**
     * General statistics report.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/general", name="stats_general")
     */
    public function generalAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/typeLocationCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $criteria = $request->request->get('report_criteria');
            $em = $this->getDoctrine()->getManager();
            // get specs to pass to template
            $specs = $this->specs($criteria);
            $reports = $this->get('mana.reports');
            $reports->setStats($specs['reportCriteria']);
            $data = $reports->getStats();
            $statistics = $data['statistics'];
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'General Statistics';
            $templates[] = 'Statistics/individualsServed.html.twig';
            $templates[] = 'Statistics/householdsServed.html.twig';
            if ('' === $specs['reportCriteria']['contact_type']) {
                $templates[] = 'Statistics/newWithoutType.html.twig';
            } else {
                $templates[] = 'Statistics/newWithType.html.twig';
            }
            $templates[] = 'Statistics/ethnicityDistribution.html.twig';
            $templates[] = 'Statistics/ageGenderDistribution.html.twig';
            $templates[] = 'Statistics/residencyDistribution.html.twig';
            if ('' === $templateSpecs['county'].$templateSpecs['center']) {
                $statistics['countyStats'] = $reports->getCountyStats();
                $templates[] = 'Statistics/countyDistribution.html.twig';
            }
            $templates[] = 'Statistics/familySizeDistribution.html.twig';
            if ($criteria['startMonth'] . $criteria['startYear'] === $criteria['endMonth'] . $criteria['endYear']) {
                $templates[] = 'Statistics/frequencyDistributionForMonth.html.twig';
            }

            $report = array(
                'excel' => 'General',
                'specs' => $templateSpecs,
                'statistics' => $statistics,
                'title' => 'General statistics',
                'reportHeader' => $this->getReportHeader($templateSpecs),
                'templates' => $templates,
            );
            $session = $request->getSession();
            $session->set('report', $report);

            return $this->render('Statistics/statistics.html.twig', $report);
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'stats_general',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select general statistics reporting criteria',
        ));
    }

    /**
     * Details report.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/details", name="stats_details")
     */
    public function detailsAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reports = $this->get('mana.reports');
            $session = $request->getSession();
            $specs = $this->specs($criteria);
            $reports->setDetails($specs['reportCriteria']);
            $data = $reports->getDetails();
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'Distribution Details';
            $templates[] = 'Statistics/details.html.twig';

            $report = array(
                'excel' => 'Details',
                'specs' => $templateSpecs,
                'details' => $data['details'],
                'title' => 'Distribution statistics',
                'reportHeader' => $this->getReportHeader($templateSpecs),
                'templates' => $templates,
            );
            $session->set('report', $report);

            return $this->render('Statistics/statistics.html.twig', $report);
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select distribution details reporting criteria',
                    'formPath' => 'stats_details',
        ));
    }

    /**
     * Multiple contact in period report
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/multi", name="multi_contacts")
     */
    public function multiAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reports = $this->get('mana.reports');
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'Multiple Same Date Contacts';
            $multi = $reports->getMultiContacts($reportSpecs);
            if (count($multi) == 0) {
                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->alert('No instances of multiple same-date contacts found');

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->render('Statistics/multi.html.twig', array('multi' => $multi,
                'title' => 'Multiple contacts',
                'reportHeader' => $this->getReportHeader($templateSpecs),
            ));
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select multiple contacts report criteria',
                    'formPath' => 'multi_contacts',
        ));
    }

    /**
     * Export Excel file.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/excel", name="stats_excel")
     */
    public function excelAction(Request $request)
    {
        $session = $request->getSession();
        $report = $session->get('report');
        $specs = $report['specs'];
        $template = $report['excel'];
        $filename = $template.'_';
        $center = !empty($specs['center']) ? $specs['center'] : '';
        $type = !empty($specs['type']) ? $specs['type'] : '';
        $county = !empty($specs['county']) ? $specs['county'] : '';
        $startText = $specs['startDate']->format('MY');
        $endText = $specs['endDate']->format('MY');
        $filename .= ($startText == $endText) ? $startText : $startText.'-'.$endText;
        $filename .= $center.$county.$type.'.xls';

        $response = $this->render('Statistics/excel'.$template.'.html.twig', $report);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * Monthly Food Bank report page.
     *
     * @param int    $month
     * @param int    $year
     *
     * @return Response
     *
     * @Route("/foodbank/{year}/{month}", name="foodbank")
     */
    public function foodbankAction($month, $year)
    {
        $criteria = array(
            'startMonth' => $month,
            'startYear' => $year,
            'endMonth' => $month,
            'endYear' => $year,
            'contact_type_id' => 0,
            'center_id' => 0,
            'county_id' => 0,
        );
        $reports = $this->get('mana.reports');
        $specs = $this->specs($criteria);
        $reports->setStats($specs['reportCriteria']);
        $data = $reports->getStats();
        $statistics = $data['statistics'];
        $ctyStats = $reports->getCountyStats();
        $report = array(
            'statistics' => $statistics,
            'ctyStats' => $ctyStats,
        );

        return $this->render('Statistics/foodbank.html.twig', $report);
    }

    /**
     * Returns report criteria
     *
     * Input consists of
     *  startMonth, startYear, endMonth, endYear
     *  optionally: center, county, contact_type, columnType
     * Output array consists of
     *  criteria for calculating data
     *  criteria to display in template
     * 
     * @param array $criteria
     *
     * @return array
     */
    private function specs($criteria)
    {
        // get specs to pass to template
        $endDay = cal_days_in_month(CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $templateCriteria['startDate'] = new \DateTime($criteria['startMonth'].'/01/'.$criteria['startYear']);
        $templateCriteria['endDate'] = new \DateTime($criteria['endMonth'].'/'.$endDay.'/'.$criteria['endYear']);
        $em = $this->getDoctrine()->getManager();

        $reportCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';
        $reportCriteria['columnType'] = (!empty($criteria['columnType'])) ? $criteria['columnType'] : '';
        $templateCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $templateCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $templateCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

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

        $reportCriteria['startDate'] = date_format($templateCriteria['startDate'], 'Y-m-d');
        $reportCriteria['endDate'] = date_format($templateCriteria['endDate'], 'Y-m-d');
        $reportCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';

        return [
            'templateCriteria' => $templateCriteria,
            'reportCriteria' => $reportCriteria,
            ];
    }

    /**
     * Employment profile.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/employmentProfile", name="employment_profile")
     */
    public function employmentProfileAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->employment($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);

            return $response;
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'employment_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Employment profile reporting criteria',
        ));
    }

    /**
     * Income profile.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/incomeProfile", name="income_profile")
     */
    public function incomeProfileAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->income($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);

            return $response;
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'income_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select income profile reporting criteria',
        ));
    }

    /**
     * Insufficient food reason profile.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/reasonProfile", name="reason_profile")
     */
    public function reasonProfileAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->reason($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);

            return $response;
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'reason_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Insufficient Food Reporting Criteria',
        ));
    }

    /**
     * Set of three profiles.
     *
     * Contains profile of Yes/No receiving foodstamps, how much, and why not
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/snapProfile", name="snap_profile")
     */
    public function snapProfileAction(Request $request)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->yesNo($reportSpecs);
            $content = $this->profilerPlain($reportData, $templateSpecs);
            $reportData = $this->howMuch($reportSpecs);
            $content .= $this->profilerPlain($reportData, $templateSpecs);
            $reportData = $this->not($reportSpecs);
            $content .= $this->profilerPlain($reportData, $templateSpecs);

            return $this->render('Statistics/snapProfile.html.twig', [
                'content' => $content,
                ]);
        }

        return $this->render('Statistics/report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'snap_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * Arranges profile data in rows and columns
     *
     * @param array $reportData
     * @param array $templateSpecs
     *
     * @return Response
     */
    private function profiler($reportData, $templateSpecs)
    {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView('Statistics/profile.html.twig', ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]);
    }

    /**
     * Arranges three SNAP profile reports
     *
     * @param array $reportData
     * @param array $templateSpecs
     *
     * @return Response
     */
    private function profilerPlain($reportData, $templateSpecs)
    {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView('Statistics/profile_content.html.twig', ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]);
    }

    /**
     * Gather row & column data for employment profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function employment($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);

        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Work')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Work')->crossTabData($dateCriteria, $columnType);

        $reportData = [
            'reportTitle' => 'Employment profile (household members)',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Employment',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Gather row & column data for SNAP how much profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function howMuch($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:FsAmount')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:FsAmount')->crossTabData($dateCriteria, $columnType);

        $reportData = [
            'reportTitle' => 'Households receiving SNAP/CalFresh benefits',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'How much',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Gather row & column data for income profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function income($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Income')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Income')->crossTabData($dateCriteria, $columnType);

        $reportData = [
            'reportTitle' => 'Household Income',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Income bracket',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Gather row & column data for SNAP why not profile
     * 
     * @param array $criteria
     * 
     * @return Response
     */
    private function not($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Notfoodstamp')
            ->rowLabels(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']]);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)
            ->colLabels(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']]);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Notfoodstamp')
            ->crossTabData(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']], $columnType);
        $reportData = [
            'reportTitle' => 'Households not receiving SNAP/CalFresh benefits',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Reason why not',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Gather row & column data for reason profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function reason($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Reason')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Reason')->crossTabData($dateCriteria, $columnType);

        $reportData = [
            'reportTitle' => 'Factors contributing to households not having enough food',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Reason',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Gather row & column data for SNAP Yes/No profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function yesNo($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = ['Yes', 'No'];
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:'.$columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:FsStatus')->crossTabData($dateCriteria, $columnType);
        $reportData = [
            'reportTitle' => 'Households receiving SNAP/CalFresh benefits',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Receiving benefits',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];

        return $reportData;
    }

    /**
     * Organize report header
     *
     * @param array $specs
     *
     * @return Response
     */
    private function getReportHeader($specs)
    {
        $startDate = date_format($specs['startDate'], 'F, Y');
        $endDate = date_format($specs['endDate'], 'F, Y');
        $line1 = $specs['reportType'].' for '.$startDate;
        $line1 .= ($startDate !== $endDate) ? ' through '.$endDate : '';
        $line1 .= '<br>';
        $line2 = '';
        if ('' !== $specs['contact_type']) {
            $line2 .= 'Type: '.$specs['contact_type'].'<br>';
        }
        $line3 = '';
        if ('' !== $specs['county']) {
            $line3 .= 'County: '.$specs['county'].'<br>';
        } elseif ('' !== $specs['center']) {
            $line3 .= 'Site: '.$specs['center'].'<br>';
        }

        return $line1.$line2.$line3;
    }
}
