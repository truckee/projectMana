<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Controller;

use Truckee\ProjectmanaBundle\Entity\Member;
use Truckee\ProjectmanaBundle\Form\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for household members
 *
 * @Route("/member")
 */
class MemberController extends Controller
{
    /**
     * Edit household member.
     *
     * @param object Request $request
     * @param int $id Member id
     *
     * @return JsonResponse
     *
     * @Route("/edit/{id}", name="member_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('TruckeeProjectmanaBundle:Member')->find($id);
        if (!$member) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $searches = $this->get('mana.searches');
        $disabledOptions = $searches->getDisabledOptions($member);
        $metadata = $searches->getMetadata($member);
        $templates[] = 'Member/memberFormRows.html.twig';
        $include = $member->getInclude();
        if (true == $include) {
            $household = $member->getHousehold();
            $headId = $household->getHead()->getId();
            if ($member->getId() === $headId) {
                array_unshift($templates, 'Member/headShowForm.html.twig');
            } else {
                array_unshift($templates, 'Member/includeForm.html.twig');
            }
        } else {
            $template = 'Member/excludedShow.html.twig';
        }

        $form = $this->createForm(MemberType::class, $member, ['disabledOptions'  => $disabledOptions]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isHead = $form['isHead']->getData();
            if (true === $isHead) {
                $household->setHead($member);
                $em->persist($household);
            }
            if (false == $member->getInclude()) {
                $member->setExcludeDate(new \DateTime());
            }
            $em->persist($member);
            $em->flush();

            $reply = array(
                'id' => $member->getId(),
                'headId' => $headId,
                'isHead' => $isHead,
                'include' => $member->getInclude(),
                'excludeDate' => ($member->getInclude()) ? null : date_format($member->getExcludeDate(), 'm/d/Y'),
                'fname' => $member->getFname(),
                'sname' => $member->getSname(),
                'dob' => date_format($member->getDob(), 'm/d/Y'),
            );
            $content = json_encode($reply);
            $response = new Response($content);

            return $response;
        }

        return $this->render('Member/edit.html.twig', [
            'form' => $form->createView(),
            'templates' => $templates,
            'id' => $id,
            'headId' => $headId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Add household member.
     *
     * @param object Request $request
     * @param int $houseId Household id
     *
     * @return JsonResponse
     *
     * @Route("/add/{houseId}", name="member_add")
     */
    public function addAction(Request $request, $houseId)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($houseId);
        if (!$household) {
            throw $this->createNotFoundException('Unable to find Household.');
        }
        $member = new Member();
        $household->addMember($member);
        $form = $this->createForm(MemberType::class, $member);
        $templates[] = 'Member/memberFormRows.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $member->setInclude(true);
            $em->persist($member);
            $em->persist($household);
            $em->flush();
            $name = $member->getFname().' '.$member->getSname();
            $view = $this->renderView('Member/memberShowBlock_content.html.twig', [
                'member' => $member,
                'hohId' => $household->getHead()->getId(),
            ]);
            $content = [
                'view' => $view,
                'name' => $name,
            ];
            $response = new Response(json_encode($content, JSON_HEX_QUOT | JSON_HEX_TAG));

            return $response;
        }

        return $this->render('Member/add.html.twig', [
            'form' => $form->createView(),
            'templates' => $templates,
            'houseId' => $houseId,
            'metadata'  => null,
        ]);
    }

    /**
     * Show household member.
     *
     * @param int $id Member id
     *
     * @return Response
     *
     * @Route("/householdMember/{id}", name="house_member")
     */
    public function memberHeadShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('TruckeeProjectmanaBundle:Member')->find($id);
        $include = $member->getInclude();
        if (true == $include) {
            $household = $member->getHousehold();
            $headId = $household->getHead()->getId();
            if ($member->getId() === $headId) {
                $template = 'Member/headShow.html.twig';
            } else {
                $template = 'Member/includeShow.html.twig';
            }
        } else {
            $template = 'Member/excludedShow.html.twig';
        }

        return $this->render($template, ['member' => $member]);
    }
}
