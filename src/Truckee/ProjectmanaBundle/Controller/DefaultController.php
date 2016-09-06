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
    public function indexAction() {
        return $this->render('Default/index.html.twig');
    }

    /**
     * Present menu of report options.
     * 
     * @return Response
     * 
     * @Route("/reportMenu", name="report_menu")
     */
    public function reportMenuAction() {
        return $this->render('Default/reportMenu.html.twig');
    }

    /**
     * Returns javascript code to generate distribution chart.
     *
     * @return script
     */
    public function scriptAction() {
        $reports = $this->get('reports');
        $chart = $reports->getDistsFYToDate();

        return $this->render('Default/script.js.twig', array(
                    'chart' => $chart,
        ));
    }

}
