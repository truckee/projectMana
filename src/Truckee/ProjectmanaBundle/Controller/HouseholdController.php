<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// src\Truckee\ProjectmanaBundle\Controller\HouseholdController.php

namespace Truckee\ProjectmanaBundle\Controller;

use Truckee\ProjectmanaBundle\Entity\Household;
use Truckee\ProjectmanaBundle\Entity\Member;
use Truckee\ProjectmanaBundle\Entity\Phone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Truckee\ProjectmanaBundle\Form\HouseholdType;
use Truckee\ProjectmanaBundle\Form\HouseholdRequiredType;
use Truckee\ProjectmanaBundle\Form\MemberType;

/**
 * Client controller.
 *
 * @Route("/household")
 */
class HouseholdController extends Controller
{

    /**
     * Finds and displays a Household entity.
     * 
     * @param object $request Request
     * @param int $id Household id
     * 
     * @return Response
     *
     * @Route("/{id}/show", name="household_show")
     */
    public function showAction(Request $request, $id)
    {
        $session = $request->getSession();
        $session->set('household', null);
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household entity.');
        }
        $templates[] = 'Member/memberShowBlock.html.twig';
        $templates[] = 'Household/show_content.html.twig';
        $templates[] = 'Address/addressShowBlock.html.twig';
        $templates[] = 'Household/contactShowBlock.html.twig';

        return $this->render('Household/show.html.twig',
                array(
                'household' => $household,
                'hohId' => $household->getHead()->getId(),
                'title' => 'Household View',
                'templates' => $templates,
        ));
    }

    /**
     * Create a new Household entity.
     * 
     * First, validate a new member to be head of household. If member name
     * closely matches an existing member name, a list of those matches is 
     * provided. Users can then either select the existing household containing 
     * that name or continue to create a new household.
     * 
     * @param object $request Request
     * 
     * @return Response
     *
     * @Route("/new", name="household_new")
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
            $id = $em->getRepository('TruckeeProjectmanaBundle:Household')->initialize($household, $head, $session);

            return $this->redirectToRoute('household_edit', array('id' => $id));
        }
        $household = new Household();
        $head = new Member();
        $new = true;
        $form = $this->createForm(HouseholdRequiredType::class, $household);
        $formHead = $this->createForm(MemberType::class, $head);
        $form->handleRequest($request);
        $formHead->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $formHead->isSubmitted() && $formHead->isValid()) {
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
            $searches = $this->get('mana.searches');
            $found = $searches->getMembers($searchFor);
            $session->set('household', $household);
            $em->detach($household);

            if (count($found) === 0) {
                //when there are no matches, create member as head with incoming data
                $id = $em->getRepository('TruckeeProjectmanaBundle:Household')->initialize($household, $head, $session);

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

                return $this->render('Member/match_results.html.twig', $match_results);
            }
        }

        return $this->render('Household/new.html.twig',
                array(
                'formType' => 'New Household',
                'form' => $form->createView(),
                'formHead' => $formHead->createView(),
                'title' => 'New Household',
        ));
    }

    /**
     * Edit existing household.
     *
     * @param object $request Request
     * @param int $id Household id
     *
     * @return Response
     * 
     * @Route("/{id}/edit", name="household_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household.');
        }
        $searches = $this->get('mana.searches');
        $disabledOptions = $searches->getDisabledOptions($household);
        $metadata = $searches->getMetadata($household);

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
        $addresses = $this->get('mana.addresses');
        $addressTemplates = $addresses->addressTemplates($household);
        $formOptions = [
            'disabledOptions' => $disabledOptions,
        ];
        $form = $this->createForm(HouseholdType::class, $household, $formOptions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hasPhysical = $form->get('physicalAddress')->get('physical')->getData();
            if ('1' === $hasPhysical) {
                $address = $form->get('physicalAddress')->getData();
                $type = $em->getRepository('TruckeeProjectmanaBundle:AddressType')->findOneBy(['addresstype' => 'Physical']);
                $address->setAddressType($type);
                $household->addAddress($address);
            }
            $hasMailing = $form->get('mailingAddress')->get('mailing')->getData();
            if ('1' === $hasMailing) {
                $address = $form->get('mailingAddress')->getData();
                $type = $em->getRepository('TruckeeProjectmanaBundle:AddressType')->findOneBy(['addresstype' => 'Mailing']);
                $address->setAddressType($type);
                $household->addAddress($address);
            }
            $em->getRepository('TruckeeProjectmanaBundle:Member')->initialize($household);
            $em->flush();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Household updated');

            return $this->redirectToRoute('household_show', array('id' => $household->getId()));
        }
        
        return $this->render('Household/edit.html.twig',
                array(
                'form' => $form->createView(),
                'title' => 'Household Edit',
                'household' => $household,
                'hohId' => $household->getHead()->getId(),
                'templates' => $addressTemplates,
                'metadata' => $metadata,
        ));
    }

    /**
     * Display results of client search.
     *
     * @param Request $request
     *
     * @return Response
     * 
     * @Route("/_search", name = "_search")
     */
    public function searchAction(Request $request)
    {
        $flash = $this->get('braincrafted_bootstrap.flash');
        $qtext = $request->query->get('qtext');
        if ($qtext == '') {
            $flash->alert('No search criteria were entered');

            return $this->redirect($request->headers->get('referer'));
        }

        if (is_numeric($qtext)) {
            // search for household id
            $em = $this->getDoctrine()->getManager();
            $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($qtext);
            if (!$household) {
                $flash->alert('Sorry, household not found');

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->redirectToRoute('household_show', array('id' => $qtext));
        } else {
            // search for head of household
            $searches = $this->get('mana.searches');
            $found = $searches->getMembers($qtext);
            if (count($found) == 0 || !$found) {
                $flash->alert('Sorry, no households were found');

                return $this->redirect($request->headers->get('referer'));
            }
            $nFound = count($found);
            if (1 == $nFound) {
                $id = $found[0]->getHousehold()->getId();

                return $this->redirectToRoute('household_show', array('id' => $id));
            } else {
                $flash->success($nFound . ' households found');
                return $this->render('Household/search.html.twig',
                        array(
                        'searchedFor' => $qtext,
                        'matched' => $found,
                        'title' => 'Search results',
                ));
            }
        }
    }

    /**
     * Get household data by id.
     * 
     * Provides a json encoded string of household head data to be displayed 
     * when adding contacts.
     * 
     * @param int $id Household id
     *
     * @return JsonResponse
     *
     * @Route("/contact/{id}", name="household_contact")
     */
    public function contactAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
        if (!$household) {
            $response = new Response('');
        } else {
            $content = $this->renderView('Contact/addHouseholdContact.html.twig',
                [
                'household' => $household,
            ]);
            $response = new Response($content);
        }

        return $response;
    }
}
