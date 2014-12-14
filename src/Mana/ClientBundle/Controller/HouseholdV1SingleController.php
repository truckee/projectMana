<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Controller\HouseholdV1Single.php

namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Mana\ClientBundle\Form\HouseholdHeadType;

/**
 * HouseholdV1Single: collect required data for head of household
 * 
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class HouseholdV1SingleController extends Controller
{

    /**
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('ManaClientBundle:Household')->find($id);
        $form = $this->createForm(new HouseholdHeadType(), $household);
        $form->handleRequest($request);

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
