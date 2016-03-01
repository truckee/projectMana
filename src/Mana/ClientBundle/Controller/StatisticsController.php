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
    public function generalAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // get specs to pass to template
            $specs = $this->specs($criteria);
            $center_id = (empty($criteria['center'])) ? 0 : $criteria['center'];
            $county_id = (empty($criteria['county'])) ? 0 : $criteria['county'];
            $stats = $this->get('reports');
            $stats->setStats($criteria);
            $data = $stats->getStats();
            $statistics = $data['statistics'];
            $reportSpecs = array_merge($specs, $data['specs']);
            $counties = $em->getRepository("ManaClientBundle:County")->findByEnabled(1);
            $ctyStats = ($county_id) ? 0 : $stats->getCountyStats();
            $ctyPcts = ($county_id || $center_id) ? 0 : $stats->getCountyPcts($statistics, $counties, $ctyStats);
            if ($ctyPcts <> 0 && $ctyStats <> 0) {
                foreach ($ctyStats as $cty => $ctyData) {
                    foreach ($ctyData as $key => $value) {
                        $ctyStats[$cty][$key . 'Pct'] = $ctyPcts[$cty][$key];
                    }
                }
            }
//
            $report = array(
                'block' => 'statsblock',
                'specs' => $reportSpecs,
                'statistics' => $statistics,
                'ctyStats' => $ctyStats,
                'ctyPcts' => $ctyPcts,
                'title' => "General statistics"
            );
            $session = $this->getRequest()->getSession();
            $session->set('report', $report);

            return $this->render("ManaClientBundle:Statistics:statistics.html.twig", $report);
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'general',
                    'formPath' => "stats_general",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select statistics reporting criteria',
        ));
    }

    /**
     * details report
     * @param type $month
     * @param type $year
     * @Route("/details", name="stats_details")
     */
    public function detailsAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $stats = $this->get('reports');
            $session = $this->getRequest()->getSession();
            $stats->setDetails($criteria);
            $data = $stats->getDetails();
            $report = array(
                'block' => 'details',
                'details' => $data['details'],
                'specs' => $data['specs'],
                'title' => 'Distribution statistics'
            );
            $session->set('report', $report);
            return $this->render("ManaClientBundle:Statistics:statistics.html.twig", $report);
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => false,
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select distribution details reporting criteria',
                    'formPath' => 'stats_details',
        ));
    }

    /**
     * @Route("/multi", name="multi_contacts")
     * @Template()
     */
    public function multiAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reports = $this->get('reports');
            $multi = $reports->getMultiContacts($criteria);
            if (count($multi) == 0) {
                $session->set('message', 'No instances of multiple same-date contacts found');
                return $this->forward("ManaClientBundle:Default:message");
            }
            return array('multi' => $multi,
                'title' => 'Multiple contacts',
            );
        }
        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => false,
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
    public function excelAction()
    {

        $session = $this->getRequest()->getSession();
        $report = $session->get('report');
        $specs = $report['specs'];
        $block = $report['block'];
        $filename = ($block == 'statsblock') ? 'General_' : 'Details_';
        $center = !empty($specs['center']) ? $specs['center'] : '';
        $type = !empty($specs['type']) ? $specs['type'] : '';
        $county = !empty($specs['county']) ? $specs['county'] : '';
        $startText = $specs['startDate']->format('MY');
        $endText = $specs['endDate']->format('MY');
        $filename .= ($startText == $endText) ? $startText : $startText . '-' . $endText;
        $filename .= $center . $county . $type . '.xls';
        $response = $this->render("ManaClientBundle:Statistics:" . $block . ".html.twig", $report);
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
        $stats = $this->get('reports');
        $stats->setStats($criteria);
        $data = $stats->getStats();
        $statistics = $data['statistics'];
        $ctyStats = $stats->getCountyStats();
        $report = array(
            'statistics' => $statistics,
            'ctyStats' => $ctyStats,
        );

        return $this->render('ManaClientBundle:Statistics:foodbank.html.twig', $report);
    }

    private function specs($criteria)
    {
        $em = $this->getDoctrine()->getManager();
        // get specs to pass to template
        $contact_type_id = (empty($criteria['contact_type'])) ? 0 : $criteria['contact_type'];
        if (!empty($contact_type_id)) {
            $typeObj = $em->getRepository('ManaClientBundle:ContactDesc')->find($contact_type_id);
            $specs['type'] = $typeObj->getContactDesc();
        }
        else {
            $specs['type'] = 0;
        }
        $center_id = (empty($criteria['center'])) ? 0 : $criteria['center'];
        if (!empty($center_id)) {
            $centerObj = $em->getRepository('ManaClientBundle:Center')->find($center_id);
            $specs['center'] = $centerObj->getCenter();
        }
        else {
            $specs['center'] = 0;
        }
        $county_id = (empty($criteria['county'])) ? 0 : $criteria['county'];
        if (!empty($county_id)) {
            $countyObj = $em->getRepository('ManaClientBundle:County')->find($county_id);
            $specs['county'] = $countyObj->getCounty();
        }
        else {
            $specs['county'] = 0;
        }

        return $specs;
    }

    /**
     * @Route("/employmentProfile", name="employment_profile")
     */
    public function employmentProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->employment($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);
            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => 'employment_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Employment profile reporting criteria',
        ));
    }

    /**
     * @Route("/incomeProfile", name="income_profile")
     */
    public function incomeProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->income($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => "income_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select income profile reporting criteria',
        ));
    }

    /**
     * @Route("/foodstampYesNoProfile", name="foodstampYesNo_profile")
     */
    public function foodstampYesNoProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->yesNo($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => "foodstampYesNo_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/foodstampHowMuchProfile", name="foodstampHowMuch_profile")
     */
    public function foodstampHowMuchProfileAction(Request $request)
    {

        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->howMuch($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => 'foodstampHowMuch_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/reasonProfile", name="reason_profile")
     */
    public function reasonProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->reason($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);

            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => "reason_profile",
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Insufficient Food Reporting Criteria',
        ));
    }

    /**
     * @Route("/notfoodstampProfile", name="notfoodstamp_profile")
     */
    public function notfoodstampProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $response = new Response();
            $reportData = $this->not($criteria);
            $content = $this->profiler($reportData);
            $response->setContent($content);
            return $response;
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => 'notfoodstamp_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    /**
     * @Route("/snapProfile", name="snap_profile")
     * @Template()
     */
    public function snapProfileAction(Request $request)
    {
        $form = $this->createForm(new ReportCriteriaType());
        $criteria = $request->request->get('report_criteria');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $reportData = $this->yesNo($criteria);
            $content = $this->profilerPlain($reportData);
            $reportData = $this->howMuch($criteria);
            $content .= $this->profilerPlain($reportData);
            $reportData = $this->not($criteria);
            $content .= $this->profilerPlain($reportData);
            return ['content' => $content];
        }

        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'extra' => 'profile',
                    'formPath' => 'snap_profile',
                    'title' => 'Report criteria',
                    'criteriaHeader' => 'Select SNAP/CalFresh benefits reporting criteria',
        ));
    }

    private function profiler($reportData)
    {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);
        $reports = $this->get('reports');
        $specs = $reports->getSpecs($reportData['criteria']);


        return $this->renderView("ManaClientBundle:Statistics:profile.html.twig", ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $specs,
        ]);
    }

    private function profilerPlain($reportData)
    {
        $xp = $this->container->get('mana.crosstab');
        $profile = $xp->crosstabQuery($reportData['data'], $reportData['rowLabels'], $reportData['colLabels']);
        $reports = $this->get('reports');
        $specs = $reports->getSpecs($reportData['criteria']);


        return $this->renderView("ManaClientBundle:Statistics:profile_content.html.twig", ['profile' => $profile,
                    'rowHeader' => $reportData['rowHeader'],
                    'rowLabels' => $reportData['rowLabels'],
                    'colLabels' => $reportData['colLabels'],
                    'reportTitle' => $reportData['reportTitle'],
                    'reportSubTitle' => $reportData['reportSubTitle'],
                    'date' => new \DateTime(),
                    'specs' => $specs,
        ]);
    }

    private function employment($criteria)
    {
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

    private function howMuch($criteria)
    {
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

    private function income($criteria)
    {
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

    private function not($criteria)
    {
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

    private function reason($criteria)
    {
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

    private function yesNo($criteria)
    {
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

}
