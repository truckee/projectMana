<?php

namespace Truckee\ProjectmanaBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Description of MenuBuilder
 *
 * @author George
 */
class MenuBuilder
{

    private $factory;
    private $checker;
    private $requestStack;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(
    FactoryInterface $factory, AuthorizationCheckerInterface $checker, RequestStack $requestStack) {
        $this->factory = $factory;
        $this->checker = $checker;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(array $options) {
        $menu = $this->factory->createItem('root');

        $request = $this->requestStack->getCurrentRequest();
        $routeName = $request->get('_route');
        if ('home' != $routeName) {
            $menu->addChild('Home', array(
                'route' => 'home',
            ));
        }
        if ($routeName === 'household_edit' || $routeName === 'household_show') {
            $id = $request->get('id');
            $menu->addChild('Add contact', array(
                'route' => 'contact_new',
                'routeParameters' => array('id' => $id),
            ));
        }

//        $menu->addChild('Home', array('route' => 'home'));

        $menu->addChild('New contacts', array(
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


        if ($this->checker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Options & users', array(
                'route' => 'easyadmin',
            ));
            $menu->addChild('Change status', array(
                'route' => 'status',
            ));
        }

        return $menu;
    }

    public function profileMenu(array $options) {
        $menu = $this->factory->createItem('root');
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

    public function reportsMenu(array $options) {
        $menu = $this->factory->createItem('root');

        $menu->addChild('General Statistics', array(
            'route' => 'stats_general',
        ));

        $menu->addChild('Distribution details', array(
            'route' => 'stats_details',
        ));

        $menu->addChild('Most recent contacts (PDF)', array(
            'route' => 'latest_contacts',
        ));

        $menu->addChild('Multiple contacts', array(
            'route' => 'multi_contacts',
        ));

        return $menu;
    }

    public function logoutMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Log out', array(
            'route' => 'logout',
        ));

        return $menu;
    }

}
