<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     * collect report criteria
     * @Route("/{dest}/criteria", name="stats_criteria")
     * @Template("ManaClientBundle:Statistics:report_criteria.html.twig")
     */
    public function criteriaAction($dest = null)
    {
        $form = $this->createForm(new ReportCriteriaType());
        return array(
            'form' => $form->createView(),
            'dest' => $dest,
            'title' => 'Report criteria'
        );
    }

    /**
     * General statistics report
     * @param Request $request
     * @param type $dest
     * @return type
     * @Route("/general", name="stats_general")
     */
    public function generalAction(Request $request)
    {

        $criteria = $request->request->get('report_criteria');

        if (empty($criteria)) {
            $session = $this->getRequest()->getSession();
            $session->set('message', 'Report criteria not available');
            return $this->forward("ManaClientBundle:Default:message");
        }

        $form = $this->createForm(new ReportCriteriaType());

        $dest = $request->request->get('dest');
        $form->handlerequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // get specs to pass to template
            $contact_type_id = (empty($criteria['contact_type_id'])) ? 0 : $criteria['contact_type_id'];
            if (!empty($contact_type_id)) {
                $typeObj = $em->getRepository('ManaClientBundle:ContactDesc')->find($contact_type_id);
                $specs['type'] = $typeObj->getContactDesc();
            }
            else {
                $specs['type'] = 0;
            }
            $center_id = (empty($criteria['center_id'])) ? 0 : $criteria['center_id'];
            if (!empty($center_id)) {
                $centerObj = $em->getRepository('ManaClientBundle:Center')->find($center_id);
                $specs['center'] = $centerObj->getCenter();
            }
            else {
                $specs['center'] = 0;
            }
            $county_id = (empty($criteria['county_id'])) ? 0 : $criteria['county_id'];
            if (!empty($county_id)) {
                $countyObj = $em->getRepository('ManaClientBundle:County')->find($county_id);
                $specs['county'] = $countyObj->getCounty();
            }
            else {
                $specs['county'] = 0;
            }

            $stats = $this->get('reports');

            $stats->setStats($criteria);

            $session = $this->getRequest()->getSession();

            $data = $stats->getStats();

            $statistics = $data['statistics'];
            $specs = array_merge($specs, $data['specs']);

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

            $report = array(
                'block' => 'statsblock',
                'statistics' => $statistics,
                'specs' => $specs,
                'ctyStats' => $ctyStats,
                'ctyPcts' => $ctyPcts,
                'title' => "General statistics"
            );

            $session->set('report', $report);
            return $this->render("ManaClientBundle:Statistics:statistics.html.twig", $report);
        }
        return $this->render('ManaClientBundle:Statistics:report_criteria.html.twig', array(
                    'form' => $form->createView(),
                    'dest' => $dest,
                    'title' => 'Report criteria'
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

        $criteria = $request->request->get('report_criteria');
        if (empty($criteria)) {
            return $this->render('ManaClientBundle:Default:message.html.twig', array(
                        'message' => 'Report criteria not available'
            ));
        }
        $form = $this->createForm(new ReportCriteriaType());
        $form->handlerequest($request);
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
                    'dest' => 'distribution',
                    'title' => 'Report criteria'
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
    
    /**
     * @Route("/incomeProfile", name="income_profile")
     * @Template()
     */
    public function xpAction()
    {
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');

        $sql = "SELECT r.center colLabel, i.income rowLabel, COUNT(DISTINCT h.id) N " .
                "FROM household h " .
                "JOIN contact c ON c.household_id = h.id " .
                "LEFT JOIN center r ON r.id = c.center_id " .
                "LEFT JOIN income i ON h.income_id = i.id " .
                "WHERE c.contact_date BETWEEN __DATE_CRITERIA__ " .
                "AND i.enabled = TRUE " .
                "GROUP BY colLabel, rowLabel";

        $rowKeys = $em->getRepository('ManaClientBundle:Income')->findBy(['enabled' => true], ['id' => 'ASC']);
        $colKeys = $em->getRepository('ManaClientBundle:Center')->activeCenters();
        $rowArray = ['keys' => $rowKeys, 'method' => 'getIncome'];
        $colArray = ['keys' => $colKeys, 'method' => 'getCenter'];

        $templateFields = [
            'rowLabel' => 'income',
            'colLabel' => 'center',
        ];
        $criteria = [
            'startMonth' => '07',
            'startYear' => '2014',
            'endMonth' => '06',
            'endYear' => '2015'];
        $query = $xp->setDateCriteria($sql, $criteria);
        $profile = $xp->crosstabQuery($query, $rowArray, $colArray);

        return ['profile' => $profile,
            'fields' => $templateFields,
            'rowHeader' => 'Income bracket',
            'rowKeys' => $rowKeys,
            'colKeys' => $colKeys,
            'reportTitle' => 'Profile: Income by Distribution Site',
            'reportSubTitle' => 'Fiscal Year to date: ',
            'date' => new \DateTime(),
            ];
    }
    

}
