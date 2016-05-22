<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mana\ClientBundle\Controller;

use Mana\ClientBundle\Form\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/member")
 */
class MemberController extends Controller
{

    /**
     * @Route("/edit/{id}", name="member_edit")
     * @Template()
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('ManaClientBundle:Member')->find($id);
        if (!$member) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $household = $member->getHousehold();
        $headId = $household->getHead()->getId();
        $templates[] = 'ManaClientBundle:Member:memberFormRows.html.twig';
        
        if ($id == $headId) {
            array_unshift($templates,'ManaClientBundle:Member:head.html.twig');
            array_push($templates,'ManaClientBundle:Member:headOffenses.html.twig');
        } else {
            array_unshift($templates, 'ManaClientBundle:Member:include.html.twig');
        }
        
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $isHead = $form['isHead']->getData();
            $household->setHead($member);
            $em->persist($household);
            $em->persist($member);
            $em->flush();
            $name = $member->getFname() . ' ' . $member->getSname();
            $response = new Response("Member " . $name . " updated");
            
            return $response;
        }

        return [
            'form' => $form->createView(),
            'templates' => $templates,
            'id' => $id,
            'headId' => $headId,
        ];
    }

}
