<?php
namespace Mana\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller {
    
    /**
     * @Route("admin", name="admin_index")
     * @Template()
     */
    public function indexAction() {
        return array(
            'title' => 'Admin menu'
        );
    }
}