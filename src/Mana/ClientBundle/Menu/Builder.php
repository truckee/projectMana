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
        }


        if ($routeName === 'household_edit' || $routeName === 'household_show') {
            $id = $request->get('id');
            $menu->addChild("Add contact", array(
                'route' => 'contact_new',
                'routeParameters' => array('id' => $id)
            ));
        }

        $menu->addChild("New contacts", array(
            'route' => 'contacts_add',
        ));

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));
        
        $menu->addChild('Reports', array(
            'route' => 'report_menu',
        ));
        
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Options & users', array(
                'route' => 'easyadmin',
            ));
        }

        return $menu;
    }

    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array(
            'route' => 'home',
        ));

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
        ));

        $menu->addChild("Distribution details", array(
            'route' => 'stats_details',
        ));

        $menu->addChild('Most recent contacts', array(
            'route' => 'center_select',
        ));

        $menu->addChild("Multiple contacts", array(
            'route' => 'multi_contacts',
        ));

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
