<?php
// src\Mana\ClientBundle\Controller\HouseholdController.php

namespace Mana\ClientBundle\Controller;

use Mana\ClientBundle\Entity\Household;
use Mana\ClientBundle\Entity\Member;
use Mana\ClientBundle\Entity\Phone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mana\ClientBundle\Form\HouseholdType;
use Mana\ClientBundle\Form\HouseholdRequiredType;
use Mana\ClientBundle\Form\MemberType;

/**
 * Client controller.
 *
 * @Route("/household")
 */
class HouseholdController extends Controller
{

    /**
     * Finds and displays a Household entity.
     * @Route("/{id}/show", name="household_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $session = $request->getSession();
        $session->set('household', null);
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household entity.');
        }
        $templates[] = "ManaClientBundle:Member:memberShowBlock.html.twig";
        $templates[] = "ManaClientBundle:Household:show_content.html.twig";
        $templates[] = "ManaClientBundle:Address:addressShowBlock.html.twig";
        $templates[] = "ManaClientBundle:Household:contactShowBlock.html.twig";

        return array(
            'household' => $household,
            'hohId' => $household->getHead()->getId(),
            'title' => 'View Household',
            'templates' => $templates,
        );
    }

    /**
     * Displays a form to create a new Household entity
     * First, validate a new member to be head of household
     * @Route("/new", name="household_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $houseTest = $session->get('household');
        if (!empty($houseTest)) {
            //household appears in session if new data selected in match_results
            $household = $em->merge($session->get('household'));
            $head = $em->merge($session->get('head'));
            $id = $em->getRepository("ManaClientBundle:Household")->initialize($household, $head, $session);
            return $this->redirectToRoute('household_edit', array('id' => $id));
        }
        $household = new Household();
        $head = new Member();
        $new = true;
        $form = $this->createForm(HouseholdRequiredType::class, $household);
        $formHead = $this->createForm(MemberType::class, $head);
        $form->handleRequest($request);
        $formHead->handleRequest($request);
        if ($form->isValid() && $formHead->isValid()) {
            // new head is not persisted until we know it's not a duplicate
            $household->addMember($head);
            $household->setHead($head);
            $household = $form->getData();
            $newHead = array(
                'fname' => $head->getFname(),
                'sname' => $head->getSname(),
                'dob' => date_format($head->getDob(), 'Y-m-d'),
            );
            $searchFor = $head->getFname() . ' ' . $head->getSname();
            $searches = $this->get('searches');
            $found = $searches->getMembers($searchFor);
            $session->set('household', $household);
            $em->detach($household);

            if (count($found) === 0) {
                //when there are no matches, create member as head with incoming data
                $id = $em->getRepository("ManaClientBundle:Household")->initialize($household, $head, $session);
                return $this->redirectToRoute('household_edit', array('id' => $id));
            } else {
                //send new data plus matches to match_results
                $session->set('head', $head);
                $em->detach($head);

                $match_results = array(
                    'newadd' => $newHead,
                    'matched' => $found,
                    'title' => 'Match Results',
                );
                return $this->render(
                        "ManaClientBundle:Member:match_results.html.twig", $match_results);
            }
        }
        return array(
            'formType' => 'New Household',
            'form' => $form->createView(),
            'formHead' => $formHead->createView(),
            'title' => 'New Household',
        );
    }

    /**
     * Display household edit form
     * @Route("/{id}/edit", name="household_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household.');
        }
        $session = $request->getSession();
        $new = false;
        if (null !== $session->get('household')) {
            $new = true;
            $session->set('household', null);
        }
        if (count($household->getPhones()) == 0) {
            $phone = new Phone();
            $household->addPhone($phone);
        }
        $form = $this->createForm(HouseholdType::class, $household, ['new' => $new]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->getRepository('ManaClientBundle:Member')->initialize($household);
            $em->persist($household);
            $em->flush();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert("Household updated");

            return $this->redirectToRoute('household_show', array('id' => $household->getId()));
        }

        return array(
            'form' => $form->createView(),
            'title' => 'Edit Household',
            'household' => $household,
            'hohId' => $household->getHead()->getId(),
        );
    }

    /**
     * Display results of client search
     * @param Request $request
     * @return type
     * @Route("/_search", name = "_search")
     */
    public function searchAction(Request $request)
    {
        $flash = $this->get('braincrafted_bootstrap.flash');
        $qtext = $request->query->get('qtext');
        if ($qtext == '') {
            $flash->alert("No search criteria were entered");
            return $this->redirect($request->headers->get('referer'));
        }

        if (is_numeric($qtext)) {
            // search for household id
            $em = $this->getDoctrine()->getManager();
            $household = $em->getRepository('ManaClientBundle:Household')->find($qtext);
            if (!$household) {
                $flash->alert('Sorry, household not found');
                return $this->redirect($request->headers->get('referer'));
            }
            return $this->redirectToRoute('household_show', array('id' => $qtext));
        } else {
            // search for head of household
            $searches = $this->get('searches');
            $found = $searches->getMembers($qtext);
            if (count($found) == 0 || !$found) {
                $flash->alert('Sorry, no households were found');
                return $this->redirect($request->headers->get('referer'));
            }

            if (count($found) == 1) {
                $id = $found[0]->getHousehold()->getId();
                return $this->redirectToRoute('household_show', array('id' => $id));
            } else {
                return $this->render('ManaClientBundle:Household:search.html.twig',
                        array(
                        'searchedFor' => $qtext,
                        'matched' => $found,
                        'title' => 'Search results',
                ));
            }
        }
    }

    /**
     * get household data by id for json response
     * @Route("/contact/{id}", name="household_contact")
     */
    public function contactAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            $response = new Response('');
        } else {
            $content = $this->renderView('ManaClientBundle:Contact:addHouseholdContact.html.twig',
                [
                'household' => $household,
            ]);
            $response = new Response($content);
        }
        
        return $response;
    }

    /**
     * Create and download registration card for household
     * @Route("/{id}/card", name="house_card")
     */
    public function cardAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        $head = $household->getHead();
        $fname = $head->getFname();
        $sname = $head->getSname();

        $offences = $head->getOffences();
        $code = "";
        foreach ($offences as $offence) {
            $violation = $offence->getOffence();
            $code .= substr($violation, 0, 1);
        }

        $filename = $sname . $fname . '_Card.pdf';

        $stylesheetXml = $this->renderView('ManaClientBundle:Household:pdfstyle.xml.twig', array());

        $facade = $this->get('ps_pdf.facade');
        $response = new Response();

        $this->render('ManaClientBundle:Household:card.pdf.twig',
            array(
            'household' => $household,
            'date' => date_create(),
            'code' => $code,
            ), $response);
        $xml = $response->getContent();
        $content = $facade->render($xml, $stylesheetXml);
        return new Response($content, 200,
            array(
            'content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ));
    }
}
