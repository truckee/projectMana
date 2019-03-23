<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src/Controller/DefaultController.php

namespace App\Controller;

use App\Services\FYChart;
use App\Services\TestReferences;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 *
 */
class DefaultController extends AbstractController {

    /**
     * Home page for authorized users
     *
     * @return Response
     *
     * @Route("/", name="home")
     */
    public function indexAction(FYChart $fiscalYearChart)
    {
        $ob = $fiscalYearChart->getDistsFYToDate();
        
        return $this->render('Default/index.html.twig', [
            'chart' => $ob,
        ]);
    }

    /**
     * Present menu of report options.
     *
     * @return Response
     *
     * @Route("/reportMenu", name="report_menu")
     */
    public function reportMenuAction()
    {
        return $this->render('Menu/reports.html.twig');
    }
}
