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

use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Truckee\ProjectmanaBundle\Entity\Household;
use Truckee\ProjectmanaBundle\Entity\Member;
use Truckee\ProjectmanaBundle\Form\HouseholdType;
use Truckee\ProjectmanaBundle\Form\MemberType;
use Truckee\ProjectmanaBundle\Utilities\PdfService;

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
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('Unable to find Household ' . $id);

            return $this->redirectToRoute('home');
        }

        $templates[] = 'Member/memberShowBlock.html.twig';
        $templates[] = 'Household/show_content.html.twig';
        $templates[] = 'Address/addressShowBlock.html.twig';
        $templates[] = 'Household/contactShowBlock.html.twig';

        return $this->render(
            'Household/show.html.twig',
            array(
                    'household' => $household,
                    'hohId' => $household->getHead()->getId(),
                    'title' => 'Household View',
                    'templates' => $templates,
                )
        );
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
        //restore original after search or create new entities
        if (null !== $session->get('household')) {
            $household = $em->merge($session->get('household'));
            $head = $em->merge($session->get('head'));
            $id = $this->initializeHousehold($household, $head, $session);

            return $this->redirectToRoute('household_edit', array('id' => $id));
        } else {
            $household = new Household();
            $head = new Member();
        }
        $form = $this->createForm(HouseholdType::class, $household);
        $formHead = $this->createForm(MemberType::class, $head);
        $form->handleRequest($request);
        $formHead->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $formHead->isSubmitted() && $formHead->isValid()) {
            $found = [];
            // do only one search for duplicate household head names
            if (null === $session->get('household')) {
                //any matches for proposed head of household?
                $searchFor = $head->getFname() . ' ' . $head->getSname();
                $searches = $this->get('mana.searches');
                $found = $searches->getMembers($searchFor);
            }
            //display possible matches
            if (0 < count($found)) {
                $session->set('household', $household);
                $em->detach($household);
                $session->set('head', $head);
                $em->detach($head);
                $newHead = array(
                    'fname' => $head->getFname(),
                    'sname' => $head->getSname(),
                    'dob' => date_format($head->getDob(), 'Y-m-d'),
                );
                $match_results = array(
                    'newadd' => $newHead,
                    'matched' => $found,
                    'title' => 'Match Results',
                );

                return $this->render('Member/match_results.html.twig', $match_results);
            }
            $id = $this->initializeHousehold($household, $head, $session);

            return $this->redirectToRoute('household_edit', array('id' => $id));
        }

        return $this->render(
            'Household/new.html.twig',
            array(
                    'formType' => 'New Household',
                    'form' => $form->createView(),
                    'formHead' => $formHead->createView(),
                    'title' => 'New Household',
                )
        );
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
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('Unable to find Household ' . $id);

            return $this->redirectToRoute('home');
        }
        $searches = $this->get('mana.searches');
        $disabledOptions = $searches->getDisabledOptions($household);
        $metadata = $searches->getMetadata($household);

//        $session = $request->getSession();
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
            $benefits = $form->get('benefits')->getData();
            //set notfoodstamp property to null if SNAP included in benefits
            if (null !== $benefits) {
                foreach ($benefits as $key => $value) {
                    if ('SNAP' === $value->getBenefit()) {
                        $household->setNotfoodstamp(null);
                    }
                }
            }
            $em->getRepository('TruckeeProjectmanaBundle:Member')->initialize($household);
            $em->persist($household);
            $em->flush();
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Household updated');

            return $this->redirectToRoute('household_show', array('id' => $household->getId()));
        }

        return $this->render(
            'Household/edit.html.twig',
            array(
                    'form' => $form->createView(),
                    'title' => 'Household Edit',
                    'household' => $household,
                    'hohId' => $household->getHead()->getId(),
                    'templates' => $addressTemplates,
                    'metadata' => $metadata,
                )
        );
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
            if (empty($request->headers->get('referer'))) {
                return $this->redirectToRoute('home');
            }

            return $this->redirect($request->headers->get('referer'));
        }

        if (is_numeric($qtext)) {
            // search for household id
            $em = $this->getDoctrine()->getManager();
            $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($qtext);
            if (!$household) {
                $flash->alert('Sorry, household not found');
                if (empty($request->headers->get('referer'))) {
                    return $this->redirectToRoute('home');
                }

                return $this->redirect($request->headers->get('referer'));
            }

            return $this->redirectToRoute('household_show', array('id' => $qtext));
        } else {
            // search for head of household
            $searches = $this->get('mana.searches');
            $found = $searches->getMembers($qtext);
            if (count($found) == 0 || !$found) {
                $flash->alert('Sorry, no households were found');
                if (empty($request->headers->get('referer'))) {
                    return $this->redirectToRoute('home');
                }

                return $this->redirect($request->headers->get('referer'));
            }
            $nFound = count($found);
            if (1 == $nFound) {
                $id = $found[0]->getHousehold()->getId();

                return $this->redirectToRoute('household_show', array('id' => $id));
            } else {
                $flash->success($nFound . ' households found');
                return $this->render(
                    'Household/search.html.twig',
                    array(
                            'searchedFor' => $qtext,
                            'matched' => $found,
                            'title' => 'Search results',
                        )
                );
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
            $content = $this->renderView(
                'Contact/addHouseholdContact.html.twig',
                [
                'household' => $household,
                ]
            );
            $response = new Response($content);
        }

        return $response;
    }

    /**
     * @Route("/turkey", name="annual_turkey")
     */
    public function annualTurkey(PdfService $pdf)
    {
        $em = $this->getDoctrine()->getManager();
        $turkeys = $em->getRepository('TruckeeProjectmanaBundle:Household')->annualTurkey();
        $year = date('Y');
        $filename = 'Let\'sTalkTurkey' . $year . '.pdf';
        $html = $this->renderView(
            'Pdf/Household/turkeyContent.html.twig',
            [
            'turkeys' => $turkeys,
            ]
        );
        $header = $this->renderView('Pdf/Household/turkeyHeader.html.twig');

        $exec = $pdf->pdfExecutable();
        $snappy = new Pdf($exec);
        $snappy->setOption('header-html', $header);
        $snappy->setOption('footer-center', 'Page [page]');
        $content = $snappy->getOutputFromHtml($html);
        $response = new Response(
            $content,
            200,
            [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=' . $filename . '.pdf',
            ]
        );

        return $response;
    }

    private function initializeHousehold($household, $head, $session)
    {
        //remove session variables no longer needed
        if (null !== $session->get('household')) {
            $session->remove('household');
            $session->remove('member');
        }
        $em = $this->getDoctrine()->getManager();
        $relation = $em->getRepository('TruckeeProjectmanaBundle:Relationship')->findOneBy(['relation' => 'Self']);
        $head->setRelation($relation);
        $head->setInclude(true);
        $em->persist($head);
        $household->addMember($head);
        $household->setHead($head);
        $em->persist($household);
        $em->flush();
        $id = $household->getId();

        return $id;
    }
}
