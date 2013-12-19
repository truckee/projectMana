<?php
// src\Mana\ClientBundle\Controller\HouseholdController.php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Mana\ClientBundle\Entity\Member;
use Mana\ClientBundle\Form\MemberType;

/**
 * Unused? Remnant of earlier version.
 * Client controller.
 * @Route("/member")
 */
class MemberController extends Controller {
    /**
     * @Route("/{id}/edit")
     * @Template("ManaClientBundle:Member:memberEdit.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('ManaClientBundle:Member')->find($id);
        if (!$member) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $form = $this->createForm(new MemberType(), $member);
        return array(
            'form' => $form->createView(),
            'member' => $member,
        );
    }
    
    /**
     * @Route("/create", name="member_create")
     * @Template("ManaClientBundle:Member:memberEdit.html.twig")
     */
    public function createAction(Request $request) {
        $member = new Member();
        $form = $this->createForm(new MemberType(), $member);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $wait = 'here';
        }
        return array(
            'form' => $form->createView(),
            'member' => $member,
        );
    }
}