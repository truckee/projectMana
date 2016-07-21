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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/status")
 */
class StatusController extends Controller
{
    /**
     * @Route("", name="status")
     * @Template()
     */
    public function showAction()
    {
        $status = $this->get('status');
        $statusYears = $status->getYearStatus();

        return array(
            'statusYears' => $statusYears,
            'title' => 'Household status',
        );
    }

    /**
     * @Route("/select", name="status_change")
     */
    public function changeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->get('status');
        $status = $this->get('status');
        $status->setStatus($data);
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->alert('Household status updated');

        return $this->redirectToRoute('status');
    }
}
