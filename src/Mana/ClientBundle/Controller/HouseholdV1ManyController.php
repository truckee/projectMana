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
 * Description of HouseholdV1Single
 * @Route("/v1")
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class HouseholdV1ManyController extends Controller
{

    /**
     * @Route("/single/{id}")
     * @Template("ManaClientBundle:HouseholdV1Many:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);

        $members = $household->getMembers();
        //$idArray required for isHead radio choices
        $idArray = array();
        $i = 0;
        foreach ($members as $member) {
            $id = $member->getId();
            $idArray[$i] = $id;
            $i++;
        }
        $form = $this->createForm(new HouseholdMembersType($idArray), $household);
        $form->handleRequest($request);
//        if ($request->getMethod() == "POST") {
//            $headData = $request->request->get('household');
//            var_dump($headData);die;
//        }
        
        
        if ($form->isValid()) {
            $em->persist($household);
            $em->flush();
            return $this->redirect($this->generateUrl('household_edit', ['id' => $id]));
        }
        $errorString = $form->getErrorsAsString();
        return [
            'household' => $household,
            'form'      => $form->createView(),
            'errorString' => $errorString,
        ];
    }

}
