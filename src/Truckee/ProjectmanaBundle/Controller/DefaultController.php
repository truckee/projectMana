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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Truckee\ProjectmanaBundle\Utilities\FYChart;

/**
 * Default controller.
 */
class DefaultController extends Controller
{

    /**
     * Home page for authorized users
     *
     * @return Response
     *
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('Default/index.html.twig');
    }

    /**
     * Present menu of report options.
     *
     * @return Response
     *
     * @Route("/reportMenu", name="report_menu")
     */
    public function reportMenuAction()
    {
        return $this->render('Default/reportMenu.html.twig');
    }

    /**
     * Returns javascript code to generate distribution chart.
     *
     * @return script
     */
    public function scriptAction(FYChart $fiscalYearChart)
    {
//        $reports = $this->get('mana.reports');
        $chart = $fiscalYearChart->getDistsFYToDate();

        return $this->render('Default/script.js.twig', array(
                    'chart' => $chart,
        ));
    }

    /**
     * Display database table documentation
     *
     * @Route("/reports/dbTables", name="db_tables")
     */
    public function dbTablesAction()
    {
        return $this->render('Default/dbTables.html.twig', [
            'title' => 'Database tables',
        ]);
    }

    /**
     * Display database option table documentation
     *
     * @Route("/reports/dbOptions", name="db_options")
     */
    public function dbOptionTablesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $addressType = $em->getRepository('TruckeeProjectmanaBundle:AddressType')->findBy([], ['addresstype' => 'ASC']);
        $center = $em->getRepository('TruckeeProjectmanaBundle:Center')->findBy([], ['center' => 'ASC']);
        $desc = $em->getRepository('TruckeeProjectmanaBundle:Contactdesc')->findBy([], ['contactdesc' => 'ASC']);
        $county = $em->getRepository('TruckeeProjectmanaBundle:County')->findBy([], ['county' => 'ASC']);
        $eth = $em->getRepository('TruckeeProjectmanaBundle:Ethnicity')->findBy([], ['ethnicity' => 'ASC']);
//        $fsa = $em->getRepository('TruckeeProjectmanaBundle:FsAmount')->findBy([], ['id' => 'ASC']);
//        $status = $em->getRepository('TruckeeProjectmanaBundle:FsStatus')->findBy([], ['status' => 'ASC']);
        $housing = $em->getRepository('TruckeeProjectmanaBundle:Housing')->findBy([], ['housing' => 'ASC']);
        $income = $em->getRepository('TruckeeProjectmanaBundle:Income')->findBy([], ['income' => 'ASC']);
        $not = $em->getRepository('TruckeeProjectmanaBundle:Notfoodstamp')->findBy([], ['notfoodstamp' => 'ASC']);
//        $offense = $em->getRepository('TruckeeProjectmanaBundle:Offence')->findBy([], ['offence' => 'ASC']);
        $reason = $em->getRepository('TruckeeProjectmanaBundle:Reason')->findBy([], ['reason' => 'ASC']);
        $state = $em->getRepository('TruckeeProjectmanaBundle:State')->findBy(['enabled' => true], ['state' => 'ASC']);
        $work = $em->getRepository('TruckeeProjectmanaBundle:Work')->findBy([], ['work' => 'ASC']);
        
        return $this->render('Default/optionTables.html.twig', [
            'title' => 'Database options',
            'addressType' => $addressType,
            'center' => $center,
            'desc' => $desc,
            'county' => $county,
            'eth' => $eth,
            'fsa' => $fsa,
            'status' => $status,
            'housing' => $housing,
            'income' => $income,
            'notFS' => $not,
            'reason' => $reason,
//            'offense' => $offense,
            'state' => $state,
            'work' => $work,
        ]);
    }
    
    /**
     * How to create db queries
     *
     * @Route("/reports/queries", name="db_queries")
     */
    public function databaseQueriesAction()
    {
        return $this->render('Default/queries.html.twig');
    }
    
    /**
     * How to install & configure MySQL Workbench
     *
     * @Route("/reports/workbench", name="db_workbench")
     */
    public function workbenchAction()
    {
        return $this->render('Default/workbench.html.twig');
    }
}
