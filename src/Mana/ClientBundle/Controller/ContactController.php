<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Contact;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Form\ContactType;
use Mana\ClientBundle\Form\SelectCenterType;
use \Mana\ClientBundle\Form\ReportCriteriaType;

/**
 * Contact controller.
 * @Route("/contact")
 */
class ContactController extends Controller {

    /**
     * Displays a form to create a new Contact entity.
     * @Route("/{id}/new", name="contact_new")
     * @Template("ManaClientBundle:Contact:contact_manage.html.twig")
     */
    public function newAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household entity.');
        }
        $contact = new Contact();
        $contact->setContactDate(date_create());
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $nContacts = count($household->getContacts());
            $first = ($nContacts > 0) ? 0 : 1;
            $contact->setFirst($first);
            $contact->setCounty($contact->getCenter()->getCounty());
            $household->addContact($contact);
            $em->persist($household);
            $em->flush();

            return $this->redirect($this->generateUrl('contact_new', array('id' => $id)));
        }

        return array(
            'form' => $form->createView(),
            'household' => $household,
            'title' => 'New Contact',
        );
    }

    /**
     * Displays a form to edit an existing Contact entity.
     * @Route("/{id}/edit", name="contact_edit")
     * @Template("ManaClientBundle:Contact:contact_manage.html.twig")
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('ManaClientBundle:Contact')->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('Unable to find Contact.');
        }
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact->setCounty($contact->getCenter()->getCounty());
            $em->persist($contact);
            $em->flush();
            $hid = $contact->getHousehold()->getId();
            return $this->redirect($this->generateUrl('contact_new', array('id' => $hid)));
        }
        return array(
            'household' => $contact->getHousehold(),
            'form' => $form->createView(),
            'contact' => $contact,
            'title' => 'Edit Contact',
        );
    }

    /**
     * Deletes a Contact entity.
     * @Route("/{id}/delete", name="contact_delete")
     * @Template("ManaClientBundle:Contact:contact_delete.html.twig")
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('ManaClientBundle:Contact')->find($id);
        $form = $this->createForm(new ContactType(), $contact);
        if ($request->isMethod('POST')) {
            $household = $contact->getHousehold();
            $hid = $household->getId();
            $household->removeContact($contact);
            $em->persist($household);
            $em->flush();
            return $this->redirect($this->generateUrl('contact_new', array('id' => $hid)));
        }
        return array(
            'contact' => $contact,
            'form' => $form->createView(),
            'title' => 'Delete Contact',
        );
    }

    /**
     * @Route("/addContacts", name="contacts_add")
     * @Template("ManaClientBundle:Contact:latestContacts.html.twig")
     */
    public function addContactsAction(Request $request) {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);
        $message = "";
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $households = $this->getRequest()->request->get('contact_household');
            $data = $form->getData();
            $contactData['date'] = $data->getContactDate();
            $center = $data->getCenter();
            $contactData['center'] = $center;
            $contactData['desc'] = $data->getContactDesc();
            $desc = $contactData['desc']->getContactDesc();
            $centerName = $center->getCenter();
            $n = count($households);
            if ($n !== 0) {
                $em->getRepository("ManaClientBundle:Household")->addContacts($households, $contactData);
                $message = "$n $desc contacts added for $centerName";
            } else {
                $message = 'No contacts were added';
            }
        }
        return array(
            'form' => $form->createView(),
            'title' => 'Add contacts',
            'message' => $message,
        );
    }

    /**
     * returning latest contacts w/ households & distribution
     * at given center
     * @Route("/latest")
     */
    public function latestContactsAction() {
        $searches = $this->get('searches');
        $found = $searches->getLatest();
        $response = new JsonResponse();
        $response->setData($found);
        return $response;
    }

    /**
     * For selected center, generates checklist of households at most recent
     * distribution
     * 
     * @Route("/centerSelect", name="center_select")
     * @Template("ManaClientBundle:Contact:centerSelect.html.twig")
     */
    public function centerSelectAction(Request $request) {
        $center = new Center();
        $form = $this->createForm(new SelectCenterType(), $center);
        $form->handleRequest($request);
        if ($form->isValid()) {
            //time limit extension required for multi-page rendering
            set_time_limit(0);
            $searches = $this->get('searches');
            $id = $center->getCenter()->getId();
            $location = $center->getCenter()->getCenter();
            $found = $searches->getRoster($id);
            if (count($found['contactSet']) == 0 || empty($found)) {
                $session = $this->getRequest()->getSession();
                $session->set('message', "No contacts found for $location");
                return $this->forward("ManaClientBundle:Default:message");
            }
            $facade = $this->get('ps_pdf.facade');
            $response = new Response();
            $this->render('ManaClientBundle:Contact:roster.html.twig', array(
                'date' => $found['latestDate'],
                'center' => $location,
                'roster' => $found['contactSet'],
                    ), $response);
            $date = new \DateTime($found['latestDate']);
            $filename = str_replace(' ', '', $location) . date_format($date, '_Ymd') . '.pdf';
            $xml = $response->getContent();
            $stylesheet = $this->renderView('ManaClientBundle:Contact:contact.xml.twig', array());
            $content = $facade->render($xml, $stylesheet);
            return new Response($content, 200, array
                ('content-type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=' . $filename
            ));
        }
        return array(
            'title' => 'Select center',
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/multi", name="multi_contacts")
     * @Template()
     */
    public function multiAction(Request $request) {
        $criteria = $request->request->get('report_criteria');
        if (empty($criteria)) {
            $session = $this->getRequest()->getSession();
            $session->set('message', 'Report criteria not available');
            return $this->forward("ManaClientBundle:Default:message");
        }
        $form = $this->createForm(new ReportCriteriaType());
        $form->handlerequest($request);
        if ($form->isValid()) {
            $reports = $this->get('reports');
            $multi = $reports->getMultiContacts($criteria);
            if (count($multi) == 0) {
                $session->set('message', 'No instances of multiple same-date contacts found');
                return $this->forward("ManaClientBundle:Default:message");
            }
            return array('multi' => $multi,
                'title' => 'Multiple contacts',
            );
        }
        return array();
    }
}
