<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     * @Template("ManaClientBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
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
        $em = $this->getDoctrine()->getManager();
        $xp = $this->container->get('mana.crosstab');

        $sql = "SELECT r.center colValue, i.income rowValue, COUNT(DISTINCT h.id) N " .
                "FROM household h " .
                "JOIN contact c ON c.household_id = h.id " .
                "LEFT JOIN center r ON r.id = c.center_id " .
                "LEFT JOIN income i ON h.income_id = i.id " .
                "WHERE c.contact_date BETWEEN __DATE_CRITERIA__ " .
                "AND i.enabled = TRUE " .
                "GROUP BY colValue, rowValue";

        $rowKeys = $em->getRepository('ManaClientBundle:Income')->findBy(['enabled' => true], ['id' => 'ASC']);
        $colKeys = $em->getRepository('ManaClientBundle:Center')->activeCenters();
        $rowArray = ['keys' => $rowKeys, 'method' => 'getIncome'];
        $colArray = ['keys' => $colKeys, 'method' => 'getCenter'];

        $templateFields = [
            'rowValue' => 'income',
            'colValue' => 'center',
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
            'rowKeys' => $rowKeys,
            'colKeys' => $colKeys];
    }

}
