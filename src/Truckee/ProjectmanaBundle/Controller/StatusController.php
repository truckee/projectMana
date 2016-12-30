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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller for bulk management of household active status
 *
 * @return Response
 *
 * @Route("/status")
 */
class StatusController extends Controller
{
    /**
     * @Route("", name="status")
     */
    public function showAction()
    {
        $status = $this->get('mana.status');
        $statusYears = $status->getYearStatus();

        return $this->render('Status/show.html.twig', array(
            'statusYears' => $statusYears,
            'title' => 'Household status',
        ));
    }

    /**
     * @Route("/select", name="status_change")
     */
    public function changeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->get('status');
        $status = $this->get('mana.status');
        $status->setStatus($data);
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->alert('Household status updated');

        return $this->redirectToRoute('status');
    }
}
