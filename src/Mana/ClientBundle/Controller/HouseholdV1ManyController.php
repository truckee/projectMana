<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Controller\HouseholdV1ManyController.php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Mana\ClientBundle\Form\HouseholdMembersType;

/**
 * Description of HouseholdV1ManyController
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class HouseholdV1ManyController extends Controller
{

    /**
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);

        $members = $household->getMembers();
        //$idArray required for isHead radio choices
        foreach ($members as $member) {
            $memberId = $member->getId();
            $idArray["$memberId"] = "$memberId";
        }
        $newHead = $this->container->get('mana.head.replacement');
        $form = $this->createForm(new HouseholdMembersType($newHead, $idArray), $household);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $headData = $request->request->get('household');
            $newHeadId = $headData['isHead'];  //new head id
            $removeThis = $em->getRepository('ManaClientBundle:Member')->find($newHeadId);
            $household->removeMember($removeThis);
            $household->setDateAdded(new \DateTime());
            $em->persist($household);
            $em->flush();

            return $this->redirect($this->generateUrl('household_edit', ['id' => $id]));
        }
        $errorString = $form->getErrorsAsString();

        return [
            'household' => $household,
            'form' => $form->createView(),
            'title' => 'Edit Household',
            'errorString' => $errorString,
        ];
    }

}
