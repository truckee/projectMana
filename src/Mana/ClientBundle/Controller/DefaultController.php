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
     * @Route("/message", name="message")
     * @Template()
     *
     * @param string $message
     */
    public function messageAction()
    {
        $session = $this->getRequest()->getSession();
        $message = $session->get('message');
        $session->set('message', '');
        return array('message' => $message);
    }

    /**
     *
     * scriptAction returns javascript code to generate distribution chart
     *
     * @return script
     */
    public function scriptAction()
    {
        $reports = $this->get('reports');
        $chart = $reports->getFiscalYearToDate();

        return $this->render('ManaClientBundle:Default:script.js.twig', array(
            'chart' => $chart,
        ));
    }
}
