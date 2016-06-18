<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Mana\ClientBundle\Form\ReportCriteriaType;

/**
 * Present various Mana statistics
 *
 * @Route("/reports")
 */
class StatisticsController extends Controller
{

    /**
     * General statistics report
     * @param Request $request
     * @param type $dest
     * @return type
     * @Route("/general", name="stats_general")
     */
    public function generalAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:typeLocationCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $criteria = $request->request->get('report_criteria');
            $em = $this->getDoctrine()->getManager();
            // get specs to pass to template
            $specs = $this->specs($criteria);
            $reports = $this->get('reports');
            $reports->setStats($specs['reportCriteria']);
            $data = $reports->getStats();
            $statistics = $data['statistics'];
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'General Statistics';
            $templates[] = 'ManaClientBundle:Statistics:individualsServed.html.twig';
            $templates[] = 'ManaClientBundle:Statistics:householdsServed.html.twig';
            if ('' === $specs['reportCriteria']['contact_type']) {
                $templates[] = 'ManaClientBundle:Statistics:newWithoutType.html.twig';
            } else {
                $templates[] = 'ManaClientBundle:Statistics:newWithType.html.twig';
            }
            $templates[] = 'ManaClientBundle:Statistics:ethnicityDistribution.html.twig';
            $templates[] = 'ManaClientBundle:Statistics:ageGenderDistribution.html.twig';
            $templates[] = 'ManaClientBundle:Statistics:residencyDistribution.html.twig';
            if ('' === $templateSpecs['county'] . $templateSpecs['center']) {
                $statistics['countyStats'] = $reports->getCountyStats();
                $templates[] = 'ManaClientBundle:Statistics:countyDistribution.html.twig';
            }
            $templates[] = 'ManaClientBundle:Statistics:familySizeDistribution.html.twig';
            if ($specs['reportCriteria']['startDate'] === $specs['reportCriteria']['endDate']) {
                $templates[] = 'ManaClientBundle:Statistics:frequencyDistributionForMonth.html.twig';
            }

            $report = array(
                'excel' => 'General',
                'specs' => $templateSpecs,
                'statistics' => $statistics,
                'title' => "General statistics",
                'reportHeader' => $this->getReportHeader($templateSpecs),
                'templates' => $templates,
            );
            $session = $request->getSession();
            $session->set('report', $report);

            return $this->render("ManaClientBundle:Statistics:statistics.html.twig", $report);
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => "stats_general",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select general statistics reporting criteria',
        ));
    }

    /**
     * details report
     * @param type $month
     * @param type $year
     * @Route("/details", name="stats_details")
     */
    public function detailsAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reports = $this->get('reports');
            $session = $request->getSession();
            $specs = $this->specs($criteria);
            $reports->setDetails($specs['reportCriteria']);
            $data = $reports->getDetails();
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'Distribution Details';
            $templates[] = 'ManaClientBundle:Statistics:details.html.twig';

            $report = array(
                'excel' => 'Details',
                'specs' => $templateSpecs,
                'details' => $data['details'],
                'title' => 'Distribution statistics',
                'reportHeader' => $this->getReportHeader($templateSpecs),
                'templates' => $templates,
            );
            $session->set('report', $report);
            return $this->render("ManaClientBundle:Statistics:statistics.html.twig", $report);
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select distribution details reporting criteria',
                    'formPath' => 'stats_details',
        ));
    }

    /**
     * @Route("/multi", name="multi_contacts")
     * @Template()
     */
    public function multiAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reports = $this->get('reports');
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $templateSpecs['reportType'] = 'Multiple Same Date Contacts';
            $multi = $reports->getMultiContacts($reportSpecs);
            if (count($multi) == 0) {
                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->alert('No instances of multiple same-date contacts found');
                
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
            
            return array('multi' => $multi,
                'title' => 'Multiple contacts',
                'reportHeader' => $this->getReportHeader($templateSpecs),
            );
        }
        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select multiple contacts report criteria',
                    'formPath' => 'multi_contacts',
        ));
    }

    /**
     * Export Excel file
     *
     * @Route("/excel", name="stats_excel")
     *
     */
    public function excelAction(Request $request) {
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

        $response = $this->render("ManaClientBundle:Statistics:excel" . $template . ".html.twig", $report);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * create monthly Food Bank report page
     * @param Request $request
     * @param type $month
     * @param type $year
     *
     * @Route("/foodbank/{year}/{month}", name="foodbank")
     *
     */
    public function foodbankAction($month, $year) {

        $criteria = array(
            'startMonth' => $month,
            'startYear' => $year,
            'endMonth' => $month,
            'endYear' => $year,
            'contact_type_id' => 0,
            'center_id' => 0,
            'county_id' => 0,
        );
        $reports = $this->get('reports');
        $specs = $this->specs($criteria);
        $reports->setStats($specs['reportCriteria']);
        $data = $reports->getStats();
        $statistics = $data['statistics'];
        $ctyStats = $reports->getCountyStats();
        $report = array(
            'statistics' => $statistics,
            'ctyStats' => $ctyStats,
        );

        return $this->render('ManaClientBundle:Statistics:foodbank.html.twig', $report);
    }

    /**
     * Returns array of 
     *      array of criteria for generating report
     *      array of criteria for templates
     * 
     * @param array $criteria 
     * @return array($contact)type, $center, $county)
     */
    private function specs($criteria) {
        // get specs to pass to template
        $endDay = cal_days_in_month (CAL_GREGORIAN, $criteria['endMonth'], $criteria['endYear']);
        $templateCriteria['startDate'] = new \DateTime($criteria['startMonth'] . '/01/' . $criteria['startYear']);
        $templateCriteria['endDate'] = new \DateTime($criteria['endMonth'] . '/'. $endDay . '/' . $criteria['endYear']);
        $em = $this->getDoctrine()->getManager();

        $reportCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $reportCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $reportCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';
        $reportCriteria['columnType'] = (!empty($criteria['columnType'])) ? $criteria['columnType'] : '';
        $templateCriteria['contact_type'] = (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '';
        $templateCriteria['center'] = (!empty($criteria['center'])) ? $criteria['center'] : '';
        $templateCriteria['county'] = (!empty($criteria['county'])) ? $criteria['county'] : '';
        
        if (!empty($templateCriteria['contact_type'])) {
            $typeObj = $em->getRepository('ManaClientBundle:ContactDesc')->find($templateCriteria['contact_type']);
            $templateCriteria['contact_type'] = $typeObj->getContactDesc();
        }
        
        if (!empty($templateCriteria['center'])) {
            $centerObj = $em->getRepository('ManaClientBundle:Center')->find($templateCriteria['center']);
            $templateCriteria['center'] = $centerObj->getCenter();
        } 
        
        if (!empty($templateCriteria['county'])) {
            $countyObj = $em->getRepository('ManaClientBundle:County')->find( $templateCriteria['county']);
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
     * @Route("/employmentProfile", name="employment_profile")
     */
    public function employmentProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
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

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'employment_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Employment profile reporting criteria',
        ));
    }

    /**
     * @Route("/incomeProfile", name="income_profile")
     */
    public function incomeProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
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

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => "income_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select income profile reporting criteria',
        ));
    }

    /**
     * @Route("/foodstampYesNoProfile", name="foodstampYesNo_profile")
     */
    public function foodstampYesNoProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->yesNo($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => "foodstampYesNo_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/foodstampHowMuchProfile", name="foodstampHowMuch_profile")
     */
    public function foodstampHowMuchProfileAction(Request $request) {

        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->howMuch($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'foodstampHowMuch_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/reasonProfile", name="reason_profile")
     */
    public function reasonProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
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

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => "reason_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Insufficient Food Reporting Criteria',
        ));
    }

    /**
     * @Route("/notfoodstampProfile", name="notfoodstamp_profile")
     */
    public function notfoodstampProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $specs = $this->specs($criteria);
            $reportSpecs = $specs['reportCriteria'];
            $templateSpecs = $specs['templateCriteria'];
            $reportData = $this->not($reportSpecs);
            $content = $this->profiler($reportData, $templateSpecs);
            $response->setContent($content);
            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'notfoodstamp_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/snapProfile", name="snap_profile")
     * @Template()
     */
    public function snapProfileAction(Request $request) {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:dateCriteria.html.twig';
        $criteriaTemplates[] = 'ManaClientBundle:Statistics:profileCriteria.html.twig';
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
            return ['content' => $content];
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'criteriaTemplates' => $criteriaTemplates,
                    'formPath' => 'snap_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    private function profiler($reportData, $templateSpecs) {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView("ManaClientBundle:Statistics:profile.html.twig", ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]);
    }

    private function profilerPlain($reportData, $templateSpecs) {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);

        return $this->renderView("ManaClientBundle:Statistics:profile_content.html.twig", ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $templateSpecs,
        ]);
    }

    private function employment($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);

        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('ManaClientBundle:Work')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:Work')->crossTabData($dateCriteria, $columnType);

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

    private function howMuch($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('ManaClientBundle:FsAmount')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:FsAmount')->crossTabData($dateCriteria, $columnType);

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

    private function income($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('ManaClientBundle:Income')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:Income')->crossTabData($dateCriteria, $columnType);

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

    private function not($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('ManaClientBundle:Notfoodstamp')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:Notfoodstamp')->crossTabData($dateCriteria, $columnType);
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

    private function reason($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = $em->getRepository('ManaClientBundle:Reason')->rowLabels($dateCriteria);
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:Reason')->crossTabData($dateCriteria, $columnType);

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

    private function yesNo($criteria) {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');
        $dateCriteria = $xp->setDateCriteria($criteria);
        $columnType = $criteria['columnType'];
        $rowLabels = ['Yes', 'No'];
        $colLabels = $em->getRepository('ManaClientBundle:' . $columnType)->colLabels($dateCriteria);
        $data = $em->getRepository('ManaClientBundle:FsStatus')->crossTabData($dateCriteria, $columnType);
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
    
    private function getReportHeader($specs) {
        $startDate = date_format($specs['startDate'], 'F, Y');
        $endDate = date_format($specs['endDate'], 'F, Y');
        $line1 = $specs['reportType'] . ' for ' . $startDate;
        $line1 .= ($startDate !== $endDate) ? ' through ' . $endDate : '';
        $line1 .= '<br>';
        $line2 = '';
        if ('' !== $specs['contact_type']) {
            $line2 .= 'Type: ' . $specs['contact_type'] . '<br>';
        }
        $line3 = '';
        if ('' !== $specs['county']) {
            $line3 .= 'County: ' . $specs['county'] . '<br>';
        } elseif ('' !== $specs['center']) {
            $line3 .= 'Site: ' . $specs['center'] . '<br>';
        }
        
        return $line1 . $line2 . $line3;
    }

}
