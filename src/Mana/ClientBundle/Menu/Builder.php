<?php

// src/Mana/ClientBundle/Menu/Builder.php

namespace Mana\ClientBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function mainMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $securityContext = $this->container->get('security.context');
        $request = $this->container->get('request');
        $routeName = $request->get('_route');

        if ($routeName <> 'home') {
            $menu->addChild('Home', array(
                'route' => 'home',
            ));
            $menu['Home']->setLinkAttribute('class', 'smallbutton');
        }


        if ($routeName === 'household_edit' || $routeName === 'household_show') {
            $id = $request->get('id');
            $menu->addChild("Add contact", array(
                'route' => 'contact_new',
                'routeParameters' => array('id' => $id)
            ));
            $menu['Add contact']->setLinkAttribute('class', 'smallbutton');
        }

        $menu->addChild('Latest contacts', array(
            'route' => 'center_select',
        ));
        $menu['Latest contacts']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild("New contacts", array(
            'route' => 'contacts_add',
        ));
        $menu["New contacts"]->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));
        $menu['New household']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('New household', array(
            'route' => 'household_new',
        ));
        $menu['New household']->setLinkAttribute('class', 'smallbutton');
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            $menu->addChild("Admin menu", array(
                'route' => 'admin_index',
            ));
            $menu['Admin menu']->setLinkAttribute('class', 'smallbutton');
        }


        $menu->addChild("Log out", array(
            'route' => 'logout',
        ));
        $menu['Log out']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function adminMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array(
            'route' => 'home',
        ));
        $menu['Home']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild("User maintenance", array(
            'route' => 'user_main',
        ));
        $menu['User maintenance']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function optionsCol1Menu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild('Appliances', array(
            'route' => 'appliance'
        ));
        $menu['Appliances']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Contact Types', array(
            'route' => 'desc'
        ));
        $menu['Contact Types']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Counties', array(
            'route' => 'county'
        ));
        $menu['Counties']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Ethnicity', array(
            'route' => 'eth'
        ));
        $menu['Ethnicity']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function optionsCol2Menu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild('Food stamp brackets', array(
            'route' => 'fsamount'
        ));
        $menu['Food stamp brackets']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Housing', array(
            'route' => 'housing'
        ));
        $menu['Housing']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Income', array(
            'route' => 'income'
        ));
        $menu['Income']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Income sources', array(
            'route' => 'incsource'
        ));
        $menu['Income sources']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function optionsCol3Menu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild('Why not foodstamps', array(
            'route' => 'notfoodstamp'
        ));
        $menu['Why not foodstamps']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Insufficient food', array(
            'route' => 'reason'
        ));
        $menu['Insufficient food']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Offences', array(
            'route' => 'offence'
        ));
        $menu['Offences']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Relationship', array(
            'route' => 'relation'
        ));
        $menu['Relationship']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function optionsCol4Menu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild('Sites', array(
            'route' => 'center'
        ));
        $menu['Sites']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('States', array(
            'route' => 'state'
        ));
        $menu['States']->setLinkAttribute('class', 'smallbutton');

        $menu->addChild('Work', array(
            'route' => 'work'
        ));
        $menu['Work']->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }

    public function reportsMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        $menu->addChild("General Statistics", array(
            'route' => 'stats_general',
//            'routeParameters' => array('dest' => 'general')
        ));
        $menu["General Statistics"]->setLinkAttribute('class', 'smallbutton');

        $menu->addChild("Distribution details", array(
            'route' => 'stats_details',
//            'routeParameters' => array('dest' => 'distribution')
        ));
        $menu["Distribution details"]->setLinkAttribute('class', 'smallbutton');

        $menu->addChild("Multiple contacts", array(
            'route' => 'multi_contacts',
//            'routeParameters' => array('dest' => 'multi')
        ));
        $menu["Multiple contacts"]->setLinkAttribute('class', 'smallbutton');

        return $menu;
    }
}
