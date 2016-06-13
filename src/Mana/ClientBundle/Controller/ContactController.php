<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Contact;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Form\ContactType;

/**
 * Contact controller.
 * @Route("/contact")
 */
class ContactController extends Controller {

    /**
     * Displays a form to create a new Contact entity.
     * @Route("/{id}/new", name="contact_new")
     * @Template("ManaClientBundle:Contact:edit.html.twig")
     */
    public function newAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household entity.');
        }
        $contact = new Contact();
        $contact->setContactDate(date_create());
        $form = $this->createForm(\Mana\ClientBundle\Form\ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $nContacts = count($household->getContacts());
            $first = ($nContacts > 0) ? 0 : 1;
            $contact->setFirst($first);
            $contact->setCounty($contact->getCenter()->getCounty());
            $household->addContact($contact);
            $em->persist($household);
            $em->flush();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert("Contact added for household $id");

            return $this->redirectToRoute('contact_new', array('id' => $id));
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
     * @Template()
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('ManaClientBundle:Contact')->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('Unable to find Contact.');
        }
        $form = $this->createForm(\Mana\ClientBundle\Form\ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact->setCounty($contact->getCenter()->getCounty());
            $em->persist($contact);
            $em->flush();
            $hid = $contact->getHousehold()->getId();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert("Contact has been updated");

            return $this->redirectToRoute('contact_new', array('id' => $hid));
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
     * @Template()
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('ManaClientBundle:Contact')->find($id);
        $form = $this->createForm(\Mana\ClientBundle\Form\ContactType::class, $contact);
        if ($request->isMethod('POST')) {
            $household = $contact->getHousehold();
            $hid = $household->getId();
            $household->removeContact($contact);
            $em->persist($household);
            $em->flush();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert("Contact has been deleted");

            return $this->redirectToRoute('contact_new', array('id' => $hid));
        }
        return array(
            'contact' => $contact,
            'form' => $form->createView(),
            'title' => 'Delete Contact',
        );
    }

    /**
     * @Route("/addContacts", name="contacts_add")
     * @Template()
     */
    public function addContactsAction(Request $request) {
        $contact = new Contact();
        $form = $this->createForm(\Mana\ClientBundle\Form\ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $households = $request->request->get('contact_household');
            $data = $form->getData();
            $contactData['date'] = $data->getContactDate();
            $center = $data->getCenter();
            $contactData['center'] = $center;
            $contactData['desc'] = $data->getContactDesc();
            $desc = $contactData['desc']->getContactDesc();
            $centerName = $center->getCenter();
            $n = count($households);
            $flash = $this->get('braincrafted_bootstrap.flash');
            if ($n !== 0) {
                $em->getRepository("ManaClientBundle:Household")->addContacts($households, $contactData);
                $flash->alert("$n $desc contacts added for $centerName");
            } else {
                $flash->alert('No contacts were added');
                return $this->redirectToRoute('contacts_add');
            }
            
        }
        return array(
            'form' => $form->createView(),
            'title' => 'Add contacts',
        );
    }

    /**
     * returning latest contacts w/ households & distribution
     * at given center
     * @Route("/latest/{site}")
     */
    public function mostRecentContactsAction($site) {
        $searches = $this->get('searches');
        $contacts = $searches->getLatest($site);
        $em = $this->getDoctrine()->getManager();
        $center = $em->getRepository('ManaClientBundle:Center')->find($site);
        $content = $this->renderView('ManaClientBundle:Contact:mostRecentContacts.html.twig', [
            'contacts' => $contacts['contacts'],
            'site' => $center,
            ]);
        $response = new Response($content);

        return $response;
    }

    /**
     * For selected center, generates checklist of households at most recent
     * distribution
     * 
     * @Route("/latestReport", name="latest_contacts")
     * @Template()
     */
    public function latestReportAction(Request $request) {
        $center = new Center();
        $form = $this->createForm(\Mana\ClientBundle\Form\SelectCenterType::class, $center);
        $form->handleRequest($request);
        if ($form->isValid()) {
            //time limit extension required for multi-page rendering
            set_time_limit(0);
            $searches = $this->get('searches');
            $id = $center->getCenter()->getId();
            $location = $center->getCenter()->getCenter();
            $found = $searches->getLatest($id);
            if (count($found['contacts']) == 0 || empty($found)) {
                $flash = $this->get('braincrafted_bootstrap.flash');
               $flash->alert("No contacts found for $location");
                return $this->redirectToRoute('latest_contacts');
            }
            $facade = $this->get('ps_pdf.facade');
            $response = new Response();
            $this->render('ManaClientBundle:Contact:roster.html.twig', array(
                'date' => $found['latestDate'],
                'center' => $location,
                'contacts' => $found['contacts'],
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
}
