<?php

// src\Mana\ClientBundle\Controller\HouseholdController.php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Mana\ClientBundle\Entity\Member;
use Mana\ClientBundle\Entity\Phone;
use Mana\ClientBundle\Entity\Household;
use Mana\ClientBundle\Form\HouseholdType;

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
     * @Template("ManaClientBundle:Household:household_show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household entity.');
        }
        return array(
            'household' => $household,
            'title' => 'View Household'
        );
    }

    /**
     * Displays a form to create a new Household entity
     * First, validate a new member to be head of household
     * @Route("/new", name="household_new")
     * @Template("ManaClientBundle:Household:household_new.html.twig")
     */
    public function newAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $houseTest = $session->get('household');
        if (!empty($houseTest)) {
            //household appears in session if new data selected in match_results
            $household = $em->merge($session->get('household'));
            $member = $em->merge($session->get('member'));
            $id = $em->getRepository("ManaClientBundle:Household")->initialize($household, $member, $session);
            return $this->redirect($this->generateUrl('household_edit', array('id' => $id)));
        }
        $household = new Household();
        $member = new Member();
        $household->addMember($member);
        $household->setHead($member);
        $form = $this->createForm(new HouseholdType(), $household);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // new head is not persisted until we know it's not a duplicate
            $household = $form->getData();
            $newMember = array(
                'fname' => $member->getFname(),
                'sname' => $member->getSname(),
                'dob' => date_format($member->getDob(), 'Y-m-d'),
            );
            $searchFor = $member->getFname() . ' ' . $member->getSname();
            $searches = $this->get('searches');
            $found = $searches->getMembers($searchFor);

            if (count($found) === 0) {
                //when there are no matches, create member as head with incoming data
                $id = $em->getRepository("ManaClientBundle:Household")->initialize($household, $member);
                return $this->redirect($this->generateUrl('household_edit', array('id' => $id)));
            }
            else {
                //send new data plus matches to match_results
                $session->set('household', $household);
                $em->detach($household);
                $session->set('member', $member);
                $em->detach($member);

                $match_results = array(
                    'newadd' => $newMember,
                    'matched' => $found,
                    'title' => 'Match Results',
                );
                return $this->render(
                                "ManaClientBundle:Household:match_results.html.twig", $match_results);
            }
        }
        $errorString = $form->getErrorsAsString();
        return array(
            'formType' => 'New Household',
            'form' => $form->createView(),
            'title' => 'New Household',
            'errorString' => $errorString,
        );
    }

    /**
     * Display household edit form
     * @Route("/{id}/edit", name="household_edit")
     * @Template("ManaClientBundle:Household:household_manage.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household.');
        }
        //flag - 0: v2; 1: v1, single member; >1: v1, >1 member
        $flag = $em->getRepository('ManaClientBundle:Household')->getHouseholdVersionFlag($id);
//        var_dump($flag);die;
        if ( 1 === $flag) {
            return $this->forward('ManaClientBundle:HouseholdV1Single:edit', ['id' => $id]);
        } elseif ( $flag > 1 ) {
            return $this->forward('ManaClientBundle:HouseholdV1Many:edit',  ['id' => $id]);
        }
//        switch ($flag) {
//            case 0;
//                break;
//            case 1:
//                return $this->forward('ManaClientBundle:HouseholdV1Single:edit', ['id' => $id]);
//            default:
//                return $this->forward('ManaClientBundle:HouseholdV1Many:edit',  ['id' => $id]);
//        }
//        var_dump($flag);die;
        // set head of household template flags
        // $v1 = true if date_added is null
        // $oneMember = single member household

        $members = $household->getMembers();
        //$idArray required for isHead radio choices
        $idArray = array();
        foreach ($members as $member) {
            $idArray[] = $member->getId();
        }
        $headData = $request->request->get('household');
        $newHeadId = $headData['isHead'];  //new head id
        $formerHeadId = $headData['headId'];  //former head id

        $flags = array();
        $headDob = $household->getHead()->getDob();
        $flags['v1'] = empty($headDob);

        if (count($household->getPhones()) == 0) {
            $phone = new Phone();
            $household->addPhone($phone);
        }

//        $flags['oneMember'] = (count($members) == 1);
        $newHead = ($newHeadId <> $formerHeadId);

        $form = $this->createForm(new HouseholdType($idArray), $household);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($newHead && $flag) {
                    $removeThis = $em->getRepository('ManaClientBundle:Member')->find($formerHeadId);
                    $household->removeMember($removeThis);
                    $household->setDateAdded(new \DateTime());
                } 
                elseif ($newHead && !$flag) {
                $hoh = $em->getRepository('ManaClientBundle:Member')->find($newHeadId);
                $household->setHead($hoh);
            }
            $em->getRepository('ManaClientBundle:Member')->initialize($household);
            $em->persist($household);
            $em->flush();
            return $this->redirect($this->generateUrl('household_show', array('id' => $household->getId())));
        }
        $hasErrors = ($request->getMethod() == 'POST') ? true : false;
        $errorString = $form->getErrorsAsString();
        return array(
            'form' => $form->createView(),
            'title' => 'Edit Household',
            'formType' => 'Edit',
            'household' => $household,
            'flag' => $flag,
//            'flags' => $flags,
            'errorString' => $errorString,
            'hasErrors' => $hasErrors,
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
        $qtext = $request->query->get('qtext');
        if ($qtext == '') {
            $session = $this->getRequest()->getSession();
            $session->set('message', "No search criteria were entered");
            return $this->forward("ManaClientBundle:Default:message");
        }

        if (is_numeric($qtext)) {
            // search for household id
            $em = $this->getDoctrine()->getManager();
            $household = $em->getRepository('ManaClientBundle:Household')->find($qtext);
            if (!$household) {
                $session = $this->getRequest()->getSession();
                $session->set('message', 'Sorry, household not found');
                return $this->forward("ManaClientBundle:Default:message");
            }
            return $this->redirect($this->generateUrl('household_show', array('id' => $qtext)));
        }
        else {
            // search for head of household
            $searches = $this->get('searches');
            $found = $searches->getMembers($qtext);
            if (count($found) == 0 || !$found) {
                $session = $this->getRequest()->getSession();
                $session->set('message', 'Sorry, no households were found');
                return $this->forward("ManaClientBundle:Default:message");
            }

            if (count($found) == 1) {
                $id = $found[0]->getHousehold()->getId();
                return $this->redirect($this->generateUrl('household_show', array('id' => $id)));
            }
            else {
                return $this->render('ManaClientBundle:Household:search.html.twig', array(
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
        $response = new JsonResponse();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        if (!$household) {
            $response->setData(0);
        }
        else {
            $contactData['id'] = $household->getId();
            $contactData['head'] = $household->getHead()->getsname() . ', ' . $household->getHead()->getFname();
            $response->setData($contactData);
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

        $this->render('ManaClientBundle:Household:card.pdf.twig', array(
            'household' => $household,
            'date' => date_create(),
            'code' => $code,
                ), $response);
        $xml = $response->getContent();
        $content = $facade->render($xml, $stylesheetXml);
        return new Response($content, 200, array(
            'content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ));
    }

}
