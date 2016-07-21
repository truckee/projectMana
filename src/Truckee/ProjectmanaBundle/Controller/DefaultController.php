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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/reportMenu", name="report_menu")
     * @Template()
     */
    public function reportMenuAction()
    {
        return array();
    }

    /**
     * scriptAction returns javascript code to generate distribution chart.
     *
     * @return script
     */
    public function scriptAction()
    {
        $reports = $this->get('reports');
        $chart = $reports->getFiscalYearToDate();

        return $this->render('TruckeeProjectmanaBundle:Default:script.js.twig', array(
                    'chart' => $chart,
        ));
    }

    /**
     * @Route("/xp")
     */
    public function exceptionAction()
    {
        throw new \Exception('An exceptional exception');

        return [];
    }
}
