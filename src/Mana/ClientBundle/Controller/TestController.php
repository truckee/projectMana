<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Mana\ClientBundle\Entity\Member;
use Mana\ClientBundle\Entity\Household;
use Mana\ClientBundle\Form\HouseholdType;
use Mana\ClientBundle\Form\MemberType;

/**
 * Test controller.
 *
 * @Route("/test")
 */
class TestController extends Controller {

    /**
     * Create and download registration card for household
     * @Route("/card", name="test_card")
     */
    public function cardAction() {
//        $em = $this->getDoctrine()->getManager();
//        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
//        $head = $household->getHead();
//        $fname = $head->getFname();
//        $sname = $head->getSname();
//
//        $offences = $head->getOffences();
//        $code = "";
//        foreach ($offences as $offence) {
//            $violation = $offence->getOffence();
//            $code .= substr($violation, 0, 1);
//        }

        $filename = 'Test_Card.pdf';

        $stylesheetXml = $this->renderView('ManaClientBundle:Test:pdfstyle.xml.twig', array());

        $facade = $this->get('ps_pdf.facade');
        $response = new Response();

        $this->render('ManaClientBundle:Test:card.pdf.twig', array(
//            'household' => $household,
//            'date' => date_create(),
//            'code' => $code,
                ), 
                $response);
        $xml = $response->getContent();
        $content = $facade->render($xml, $stylesheetXml);
        return new Response($content, 200, array(
            'content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ));
    }


}
