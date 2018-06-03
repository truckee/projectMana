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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Truckee\ProjectmanaBundle\Form\ReportCriteriaType;
use Truckee\ProjectmanaBundle\Utilities\CountyStatistics;
use Truckee\ProjectmanaBundle\Utilities\CriteriaBuilder;
use Truckee\ProjectmanaBundle\Utilities\Crosstab;
use Truckee\ProjectmanaBundle\Utilities\DetailsReport as Detail;
use Truckee\ProjectmanaBundle\Utilities\GeneralStatisticsReport as General;
use Truckee\ProjectmanaBundle\Utilities\TempTables;

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
    public function generalAction(Request $request, CountyStatistics $countyStats, CriteriaBuilder $builder, General $general)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/typeLocationCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getGeneralCriteria($formCriteria);
            $statistics = $general->getGeneralStats($criteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);
            $templateCriteria['reportType'] = 'General Statistics';
            $templates[] = 'Statistics/individualsServed.html.twig';
            $templates[] = 'Statistics/householdsServed.html.twig';
            if ([] === $criteria['contactParameters']) {
                $templates[] = 'Statistics/newWithoutType.html.twig';
            } else {
                $templates[] = 'Statistics/newWithType.html.twig';
            }
            $templates[] = 'Statistics/ethnicityDistribution.html.twig';
            $templates[] = 'Statistics/ageGenderDistribution.html.twig';
            if ([] === $criteria['siteParameters']) {
                $statistics['countyStats'] = $countyStats->getCountyStats($criteria);
                $templates[] = 'Statistics/countyDistribution.html.twig';
            }
            $templates[] = 'Statistics/familySizeDistribution.html.twig';
            if ($formCriteria['startMonth'] . $formCriteria['startYear'] === $formCriteria['endMonth'] . $formCriteria['endYear']) {
                $templates[] = 'Statistics/frequencyDistributionForMonth.html.twig';
            }
            $report = array(
                'excel' => 'General',
                'specs' => $templateCriteria,
                'statistics' => $statistics,
                'title' => 'General statistics',
                'reportHeader' => $this->getReportHeader($templateCriteria),
                'templates' => $templates,
            );
            $session = $request->getSession();
            $session->set('report', $report);

            return $this->render('Statistics/statistics.html.twig', $report);
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'stats_general',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select general statistics reporting criteria',
        )
        );
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
    public function detailsAction(Request $request, CriteriaBuilder $builder, Detail $detail, TempTables $tables)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $data = $detail->getDetailStatistics($criteria);

            $session = $request->getSession();
            $templateCriteria = $builder->getTemplateCriteria($criteria);
            $templateCriteria['reportType'] = 'Distribution Details';
            $templates[] = 'Statistics/details.html.twig';

            $report = array(
                'excel' => 'Details',
                'specs' => $templateCriteria,
                'details' => $data,
                'title' => 'Distribution statistics',
                'reportHeader' => $this->getReportHeader($templateCriteria),
                'templates' => $templates,
            );
            $session->set('report', $report);

            return $this->render('Statistics/statistics.html.twig', $report);
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select distribution details reporting criteria',
                    'formPath' => 'stats_details',
        )
        );
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
    public function multiAction(Request $request, CriteriaBuilder $builder)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);
            $templateCriteria['reportType'] = 'Multiple Same Date Contacts';
            $em = $this->getDoctrine()->getManager();
            $multi = $em->getRepository('TruckeeProjectmanaBundle:Contact')->getMultiContacts($criteria);
            if (count($multi) == 0) {
                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->alert('No instances of multiple same-date contacts found');

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->render(
                'Statistics/multi.html.twig',
                    array('multi' => $multi,
                        'title' => 'Multiple contacts',
                        'reportHeader' => $this->getReportHeader($templateCriteria),
            )
            );
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select multiple contacts report criteria',
                    'formPath' => 'multi_contacts',
        )
        );
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
        $filename = $template . '_';
        $center = !empty($specs['center']) ? $specs['center'] : '';
        $type = !empty($specs['type']) ? $specs['type'] : '';
        $county = !empty($specs['county']) ? $specs['county'] : '';
        $startText = $specs['startDate']->format('MY');
        $endText = $specs['endDate']->format('MY');
        $filename .= ($startText == $endText) ? $startText : $startText . '-' . $endText;
        $filename .= $center . $county . $type . '.xls';

        $response = $this->render('Statistics/excel' . $template . '.html.twig', $report);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
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
    public function foodbankAction($month, $year, CriteriaBuilder $builder, General $general, CountyStatistics $countyStats)
    {
        $foodbankCriteria = array(
            'startMonth' => $month,
            'startYear' => $year,
            'endMonth' => $month,
            'endYear' => $year,
            'contactdesc_id' => 0,
            'center_id' => 0,
            'county_id' => 0,
        );
        $criteria = $builder->getDetailsCriteria($foodbankCriteria);
        $tableCriteria = $criteria['table'];
        //$reportCriteria = $criteria['report'];
        $statistics = $general->getGeneralStats($tableCriteria, $reportCriteria);
        $ctyStats = $countyStats->getCountyStats();
        $report = array(
            'statistics' => $statistics,
            'ctyStats' => $ctyStats,
        );

        return $this->render('Statistics/foodbank.html.twig', $report);
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
    public function employmentProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $criteria['columnType'] = $formCriteria['columnType'];
            $templateCriteria = $builder->getTemplateCriteria($criteria);
            $response = new Response();
            $reportData = $this->employment($criteria, $crosstab);
            $content = $this->profiler($reportData, $templateCriteria, $crosstab);
            $response->setContent($content);

            return $response;
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'employment_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Employment profile reporting criteria',
        )
        );
    }

    /**
     * Housing profile.
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/housingProfile", name="housing_profile")
     */
    public function housingProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $criteria['columnType'] = $formCriteria['columnType'];
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $response = new Response();
            $reportData = $this->housing($criteria, $crosstab);
            $content = $this->profiler($reportData, $templateCriteria, $crosstab);
            $response->setContent($content);

            return $response;
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'housing_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Housing profile reporting criteria',
        )
        );
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
    public function incomeProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $criteria['columnType'] = $formCriteria['columnType'];
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $response = new Response();
            $reportData = $this->income($criteria, $crosstab);
            $content = $this->profiler($reportData, $templateCriteria, $crosstab);
            $response->setContent($content);

            return $response;
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'income_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select income profile reporting criteria',
        )
        );
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
    public function reasonProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $criteria['columnType'] = $formCriteria['columnType'];
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $response = new Response();
            $reportData = $this->reason($criteria, $crosstab);
            $content = $this->profiler($reportData, $templateCriteria, $crosstab);
            $response->setContent($content);

            return $response;
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'reason_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Insufficient Food Reporting Criteria',
        )
        );
    }

    /**
     * Set of three SNAP related profiles.
     *
     * Contains profile of Yes/No receiving foodstamps, how much, and why not
     *
     * @param object Request $request
     *
     * @return Response
     *
     * @Route("/snapProfile", name="snap_profile")
     */
    public function snapProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $criteria['columnType'] = $formCriteria['columnType'];
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $reportData = $this->yesNo($criteria, $crosstab);
            $content = $this->profiler($reportData, $templateCriteria, $crosstab);
            $reportData = $this->howMuch($criteria, $crosstab);
            $content .= $this->profiler($reportData, $templateCriteria, $crosstab);
            $reportData = $this->not($criteria);
            $content .= $this->profiler($reportData, $templateCriteria, $crosstab);

            return $this->render('Statistics/snapProfile.html.twig', [
                        'content' => $content,
            ]);
        }

        return $this->render(
            'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'snap_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        )
        );
    }

    /**
     * Arranges profile data in rows and columns
     *
     * @param array $reportData
     * @param array $templateSpecs
     *
     * @return Response
     */
    private function profiler($reportData, $templateSpecs, $crosstab)
    {
        $profile = $crosstab->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView(
            'Statistics/profile.html.twig',
                ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]
        );
    }

    /**
     * Arranges three SNAP profile reports
     *
     * @param array $reportData
     * @param array $templateSpecs
     *
     * @return Response
     */
    private function profilerPlain($reportData, $templateSpecs, $crosstab)
    {
        $profile = $crosstab->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView(
            'Statistics/profile_content.html.twig',
                ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]
        );
    }

    /**
     * Gather row & column data for employment profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function employment($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);

        //$columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Work')->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Work')->crossTabData($criteria, $criteria['columnType']);

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
     * Gather row & column data for housing profile
     *
     * @param array $criteria
     *
     * @return Response
     */
    private function housing($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);

        //$columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Housing')->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Housing')->crossTabData($criteria, $criteria['columnType']);

        $reportData = [
            'reportTitle' => 'Housing profile (household members)',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Housing',
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
    private function howMuch($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);
        //$columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:FsAmount')->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:FsAmount')->crossTabData($criteria, $criteria['columnType']);

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
    private function income($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);
        //$columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Income')->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Income')->crossTabData($criteria, $criteria['columnType']);

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
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Notfoodstamp')
            ->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))
            ->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Notfoodstamp')
            ->crossTabData($criteria, $criteria['columnType']);
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
    private function reason($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);
        //$columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Reason')->rowLabels($criteria);
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Reason')->crossTabData($criteria, $criteria['columnType']);

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
    private function yesNo($criteria, $crosstab)
    {
        $em = $this->getDoctrine()->getManager();
        //$dateCriteria = $crosstab->setDateCriteria($criteria);
        //$columnType = $criteria['columnType'];
        $rowLabels = ['Yes', 'No'];
        $colLabels = $em->getRepository('TruckeeProjectmanaBundle:' . ucfirst($criteria['columnType']))->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:FsStatus')->crossTabData($criteria, $criteria['columnType']);
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
     * Excel-compatible html report header
     *
     * @return string
     */
    private function getReportHeader($templateCriteria)
    {
        $startDate = date_format($templateCriteria['startDate'], 'F, Y');
        $endDate = date_format($templateCriteria['endDate'], 'F, Y');
        $line1 = $templateCriteria['reportType'] . ' for ' . $startDate;
        $line1 .= ($startDate !== $endDate) ? ' through ' . $endDate : '';
        $line2 = isset($templateCriteria['contactdesc']) ? '<br>' . $templateCriteria['contactdesc'] : '';
        $line3 = isset($templateCriteria['site']) ? '<br>' . $templateCriteria['site'] : '';

        return $line1 . $line2 . $line3;
    }
}
