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
    public function detailsAction(Request $request, CriteriaBuilder $builder, Detail $detail)
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
//                'excel' => 'Details',
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
            'contactdesc' => '',
            'center' => '',
            'county' => '',
        );
        $criteria = $builder->getGeneralCriteria($foodbankCriteria);
        $statistics = $general->getGeneralStats($criteria);
        $ctyStats = $countyStats->getCountyStats($criteria);
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
            $profileParameters = [
                'entity' => 'work',
                'entityField' => 'job',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $em = $this->getDoctrine()->getManager();
            $rowLabels = $em->getRepository('TruckeeProjectmanaBundle:Work')->rowLabels($criteria);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $em->getRepository('TruckeeProjectmanaBundle:Work')->crossTabData($criteria);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Employment profile (household members)',
                        'rowHeader' => 'Employment',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
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
            $profileParameters = [
                'entity' => 'housing',
                'entityField' => 'housing',
                'joinField' => 'housing',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $rowLabels = $crosstab->rowLabels($criteria, $profileParameters);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $crosstab->crosstabData($criteria, $profileParameters);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Housing profile (household members)',
                        'rowHeader' => 'Housing',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
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
            $profileParameters = [
                'entity' => 'income',
                'entityField' => 'income',
                'joinField' => 'income',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $rowLabels = $crosstab->rowLabels($criteria, $profileParameters);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $crosstab->crosstabData($criteria, $profileParameters);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Household Income',
                        'rowHeader' => 'Income bracket',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
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
            $profileParameters = [
                'entity' => 'reason',
                'entityField' => 'reason',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $rowLabels = $crosstab->mtmRowLabels($criteria, $profileParameters);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $crosstab->mtmCrosstabData($criteria, $profileParameters);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Factors contributing to households not having enough food',
                        'rowHeader' => 'Reason',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
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
     * Set of SNAP related profiles.
     *
     * Contains profile of Yes/No receiving food stamps and why not
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
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];
            $wtf = $this->getDoctrine()->getManager()->getRepository('TruckeeProjectmanaBundle:Benefit')->crossTabData($criteria);
            $benefit = $this->benefit($criteria, $crosstab);
            $content = $this->profilerPlain($benefit, $templateCriteria, $crosstab);
            $not = $this->not($criteria, $crosstab);
            $content .= $this->profilerPlain($not, $templateCriteria, $crosstab);

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
     * @Route("/assistanceProfile", name="assistance_profile")
     */
    public function assistanceProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profileParameters = [
                'entity' => 'assistance',
                'entityField' => 'assistance',
                'joinField' => 'assistances',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $rowLabels = $crosstab->mtmRowLabels($criteria, $profileParameters);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $crosstab->mtmCrosstabData($criteria, $profileParameters);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Seeking services',
                        'rowHeader' => 'Service',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
        }

        return $this->render(
                'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'assistance_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Seeking Services Reporting Criteria',
                )
        );
    }

    /**
     * @Route("/organizationProfile", name="organization_profile")
     */
    public function organizationProfileAction(Request $request, CriteriaBuilder $builder, Crosstab $crosstab)
    {
        $form = $this->createForm(ReportCriteriaType::class);
        $criteriaTemplates[] = 'Statistics/dateCriteria.html.twig';
        $criteriaTemplates[] = 'Statistics/profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profileParameters = [
                'entity' => 'organization',
                'entityField' => 'organization',
                'joinField' => 'organizations',
            ];
            $formCriteria = $request->request->get('report_criteria');
            $criteria = $builder->getDetailsCriteria($formCriteria);
            $templateCriteria = $builder->getTemplateCriteria($criteria);

            $criteria['columnType'] = $formCriteria['columnType'];

            $rowLabels = $crosstab->mtmRowLabels($criteria, $profileParameters);
            $colLabels = $crosstab->colLabels($criteria);
            $rawData = $crosstab->mtmCrosstabData($criteria, $profileParameters);
            $profile = $crosstab->crosstabQuery($rawData, $rowLabels, $colLabels);

            return $this->render(
                    'Statistics/profile.html.twig',
                    [
                        'colLabels' => $colLabels,
                        'date' => new \DateTime(),
                        'profile' => $profile,
                        'reportSubTitle' => 'For the period ',
                        'reportTitle' => 'Receiving services',
                        'rowHeader' => 'Organization',
                        'rowLabels' => $rowLabels,
                        'specs' => $templateCriteria,
                    ]
            );
        }

        return $this->render(
                'Statistics/report_criteria.html.twig',
                array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'organization_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Receiving Services Reporting Criteria',
                )
        );
    }

    /**
     * @Route("/otherServices", name="other_services")
     */
    public function otherServicesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $seekingServices = $em->getRepository('TruckeeProjectmanaBundle:Household')->seekingServices();
        $seeking = [];
        if (!empty($seekingServices)) {
            foreach ($seekingServices as $sElement) {
                $seeking[$sElement['seeking']][] = $sElement['id'];
            }
        }
        $receivingServices = $em->getRepository('TruckeeProjectmanaBundle:Household')->receivingServices();
        $receiving = [];
        if (!empty($receivingServices)) {
            foreach ($receivingServices as $rElement) {
                $receiving[$rElement['receiving']][] = $rElement['id'];
            }
        }
        
        return $this->render('Statistics/otherServices.html.twig', [
                    'seekingServices' => $seeking,
                    'receivingServices' => $receiving,
        ]);
    }

    /**
     * Arranges SNAP profile reports
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
     * Gather row & column data for SNAP why not profile
     */
    private function not($criteria, $crosstab)
    {
        $profileParameters = [
            'entity' => 'notfoodstamp',
            'entityField' => 'notfoodstamp',
            'joinField' => 'notfoodstamp',
        ];
        $rowLabels = $crosstab->rowLabels($criteria, $profileParameters);
        $colLabels = $crosstab->colLabels($criteria);
        $data = $crosstab->crosstabData($criteria, $profileParameters);

        return [
            'reportTitle' => 'Households not receiving SNAP/CalFresh benefits',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Reason why not',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];
    }

    private function benefit($criteria, $crosstab)
    {
//        $profileParameters = [
//            'entity' => 'notfoodstamp',
//            'entityField' => 'notfoodstamp',
//            'joinField' => 'notfoodstamp',
//        ];
        $em = $this->getDoctrine()->getManager();
        $rowLabels = ['Yes', 'No'];
        $colLabels = $crosstab->colLabels($criteria);
        $data = $em->getRepository('TruckeeProjectmanaBundle:Benefit')->crossTabData($criteria);

        return [
            'reportTitle' => 'Households receiving SNAP/CalFresh benefits',
            'reportSubTitle' => 'For the period ',
            'criteria' => $criteria,
            'rowHeader' => 'Receiving benefits',
            'rowLabels' => $rowLabels,
            'colLabels' => $colLabels,
            'data' => $data,
        ];
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
