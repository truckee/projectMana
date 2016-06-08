<?php

// src/Mana/ClientBundle/Menu/Builder.php

namespace Mana\ClientBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $securityContext = $this->container->get('security.authorization_checker');
        $request = $this->container->get('request');
        $routeName = $request->get('_route');

        if ($routeName <> 'home') {
            $menu->addChild('Home', array(
                'route' => 'home',
            ));
//            $menu['Home'];
        }


        if ($routeName === 'household_edit' || $routeName === 'household_show') {
            $id = $request->get('id');
            $menu->addChild("Add contact", array(
                'route' => 'contact_new',
                'routeParameters' => array('id' => $id)
            ));
//            $menu['Add contact'];
        }

        $menu->addChild('Latest contacts', array(
            'route' => 'center_select',
        ));
//        $menu['Latest contacts'];

        $menu->addChild("New contacts", array(
            'route' => 'contacts_add',
        ));
//        $menu["New contacts"];

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));
//        $menu['New household'];

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));
//        $menu['New household'];
        
        $menu->addChild('Reports', array(
            'route' => 'report_menu',
        ));
//        $menu['Reports'];
        
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Options & users', array(
                'route' => 'easyadmin',
            ));
//            $menu['Options & users'];
        }


//        $menu['Log out'];

        return $menu;
    }

    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array(
            'route' => 'home',
        ));
//        $menu['Home'];

//        $menu->addChild("Options maintenance", array(
//            'route' => 'easyadmin',
//        ));
//        $menu['Options maintenance'];

//        $menu->addChild("User maintenance", array(
//            'route' => 'user_main',
//        ));
//        $menu['User maintenance'];

        return $menu;
    }

    public function profileMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Employment', array(
            'route' => 'employment_profile',
        ));
        $menu->addChild('Income', array(
            'route' => 'income_profile',
        ));
        $menu->addChild('Insusfficient food', array(
            'route' => 'reason_profile',
        ));
        $menu->addChild('SNAP', array(
            'route' => 'snap_profile',
        ));

        return $menu;
    }

    public function reportsMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild("General Statistics", array(
            'route' => 'stats_general',
//            'routeParameters' => array('dest' => 'general')
        ));
        $menu["General Statistics"];

        $menu->addChild("Distribution details", array(
            'route' => 'stats_details',
//            'routeParameters' => array('dest' => 'distribution')
        ));
        $menu["Distribution details"];

        $menu->addChild("Multiple contacts", array(
            'route' => 'multi_contacts',
//            'routeParameters' => array('dest' => 'multi')
        ));
        $menu["Multiple contacts"];

        return $menu;
    }

    public function logoutMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild("Log out", array(
            'route' => 'logout',
        ));

        return $menu;
    }
}
