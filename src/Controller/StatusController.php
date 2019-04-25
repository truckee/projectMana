<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Services\Status;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for bulk management of household active status
 *
 * @return Response
 *
 * @Route("/status")
 */
class StatusController extends AbstractController
{

    /**
     * @Route("", name="status")
     */
    public function showAction(Status $status)
    {
        $statusYears = $status->getYearStatus();

        return $this->render('Status/show.html.twig', array(
                    'statusYears' => $statusYears,
                    'title' => 'Household status',
        ));
    }

    /**
     * @Route("/select", name="status_change")
     */
    public function changeAction(Request $request, Status $status)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->get('status');
        $status->setStatus($data);
        $this->addFlash(
            'info',
            'Household status updated'
        );
        return $this->redirectToRoute('status');
    }
}
