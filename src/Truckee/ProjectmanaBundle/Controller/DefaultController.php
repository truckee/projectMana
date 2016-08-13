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

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('Default/index.html.twig');
    }

    /**
     * @Route("/reportMenu", name="report_menu")
     */
    public function reportMenuAction()
    {
        return $this->render('Default/reportMenu.html.twig');
    }

    /**
     * scriptAction returns javascript code to generate distribution chart.
     *
     * @return script
     */
    public function scriptAction()
    {
        $reports = $this->get('reports');
        $chart = $reports->getDistsFYToDate();

        return $this->render('Default/script.js.twig', array(
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
