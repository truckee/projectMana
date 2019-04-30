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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/mailtest", name="mailtest")
 * @IsGranted("ROLE_ADMIN")
 * 
 */
class ErrorMailController extends Controller
{
    /**
     * 
     * @Route("/")
     */
    public function indexAction()
    {
        // Test non-existent class
        throw new Exception("What are you doing, Dave?");
    }
}
