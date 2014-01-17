<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Mana\ClientBundle\Form\StatusType;

/**
 * @Route("/status")
 */
class StatusController extends Controller {

    /**
     * @Route("", name="status")
     * @Template()
     */
    public function showAction() {
        $status = $this->get('status');
        $statusYears = $status->getYearStatus();
        return array(
            'statusYears' => $statusYears,
            'title' => 'Household status',
        );
    }

    /**
     * @Route("/select", name="status_change")
     * @return form object 
     * @Template("ManaClientBundle:Default:status_select.html.twig") 
     */
    public function changeAction(Request $request) {
//        $em = $this->getDoctrine()->getManager();
        $data = $this->get('request')->request->get('status');
        $status = $this->get('status');
        $status->setStatus($data);
        return $this->redirect($this->generateUrl("status"));
    }

    /**
     * @Route("/update", name="status_update") 
     */
    public function updateAction(Request $request) {
        $action = $this->get('request')->request->get('submit');
        if ($action == 'Confirm') {
            $status = $this->get('request')->request->get('status');
            $year = $this->get('request')->request->get('year');
            $change = $this->changeStatus($status, $year);
            $message = ($change) ? 'Client status changed' : 'Status change not successful';
            return $this->render('ManaClientBundle:Default:message.html.twig', array(
                        'message' => $message
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
}
