<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/home", name="home")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/reportMenu", name="report_menu")
     * @Template()
     */
    public function reportMenuAction() {
        return array();
    }

    /**
     * scriptAction returns javascript code to generate distribution chart.
     *
     * @return script
     */
    public function scriptAction() {
        $reports = $this->get('reports');
        $chart = $reports->getFiscalYearToDate();

        return $this->render('ManaClientBundle:Default:script.js.twig', array(
                    'chart' => $chart,
        ));
    }

    /**
     * @Route("/xp")
     */
    public function exceptionAction() {
        throw new \Exception('An exceptional exception');
        
        return [];
    }

}
