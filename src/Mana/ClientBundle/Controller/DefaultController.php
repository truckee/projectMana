<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {

    /**
     * @Route("/")
     * @Template("ManaClientBundle:Default:index.html.twig")
     */
    public function indexAction() {
        return array();
    }

    /**
     * mail test
     * @return e-mail
     * 
     * @Route("/errormail", name="errormail") 
     */
    public function errorAction() {
        $location = $_SERVER['REMOTE_ADDR'];
        if ($location == '127.0.0.1' || '192.168.168.182') {
            $recipient = 'developer@bogus.info';
        } else {
            
        }
		$recipient = 'truckeetrout@yahoo.com';
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
    public function messageAction() {
        $session = $this->getRequest()->getSession();
        $message = $session->get('message');
        $session->set('message', '');
        return array('message' => $message);
    }
}
