<?php
namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends Controller {
    
    /**
    * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin", name="admin_index")
     * @Template()
     */
    public function indexAction() {
        return array(
            'title' => 'Admin menu'
        );
    }
}