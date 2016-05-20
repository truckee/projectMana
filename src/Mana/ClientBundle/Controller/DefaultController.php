<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Form\MemberType;
use Mana\ClientBundle\Entity\Member;

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
    public function reportMenuAction() {
        return array();
    }

    /**
     * mail test
     * @return e-mail
     *
     * @Route("/errormail", name="errormail")
     */
    public function errorAction()
    {
        $location = $_SERVER['REMOTE_ADDR'];
        if ($location == '127.0.0.1' || '192.168.168.182') {
            $recipient = 'developer@bogus.info';
        }
        else {

        }
//        $recipient = 'truckeetrout@yahoo.com';
        $error = 'The following kerfuffle has occurred.' . "/n";
        $mail = \Swift_Message::newInstance()
                ->setSubject('Project MANA error')
                ->setFrom('error_prone@projectmana.org')
                ->setTo($recipient)
                ->setBody(
                $this->renderView('ManaClientBundle:Default:error_mail.html.twig', array(
                    'error' => $error,
                ))
                )
        ;
        $wasSent = $this->get('mailer')->send($mail);

        $good = 'An error has occurred; support has been notified.';
        $bad = "Please contact support regarding this message";

        $message = ($wasSent) ? $good : $bad;

        return $this->render('ManaClientBundle:Default:message.html.twig', array(
                    'message' => $message
        ));
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
     * @Route("/xp")
     * @Template()
     */
    public function xpAction()
    {
        throw new \Exception('A purposely thrown PHP exception');
    }
    
    public function scriptAction() {
        $reports = $this->get('reports');
        $chart = $reports->getFiscalYearToDate();

        return $this->render('ManaClientBundle:Default:script.js.twig', array(
            'chart' => $chart,
        ));
    }

    /**
     * @Route("test", name="test")
     * @Template()
     */
    public function testMember() {
        $member = new Member();
        $form = $this->createForm(new MemberType(), $member);
        return array(
            'form' => $form->createView(),
        );
    }
}
