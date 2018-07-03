<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Description of MenuBuilder
 *
 * @author George Brooks
 */
class MenuBuilder
{
    private $factory;
    private $checker;
    private $requestStack;

    /**
     *
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $checker
     * @param RequestStack $requestStack
     */
    public function __construct(
    FactoryInterface $factory,
        AuthorizationCheckerInterface $checker,
        RequestStack $requestStack
    ) {
        $this->factory = $factory;
        $this->checker = $checker;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $request = $this->requestStack->getCurrentRequest();
        $routeName = $request->get('_route');
        if ('home' != $routeName) {
            $menu->addChild('Home', array(
                'route' => 'home',
            ));
        }

        $menu->addChild('New contacts');
        $menu['New contacts']->addChild('Most recent', [
             'route' => 'contacts_add',
            'routeParameters' => ['source' => 'Most recent'],
        ]);
        $menu['New contacts']->addChild('FY to date', [
             'route' => 'contacts_add',
            'routeParameters' => ['source' => 'FY to date'],
        ]);

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

    public function householdMenu(array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');
        $id = $request->get('id');
        $menu = $this->factory->createItem('root');
        $title = 'Household contacts';
        $menu->addChild(
            $title,
            array(
            'route' => 'contact_new',
            'routeParameters' => array('id' => $id),
        )
        );
        $menu[$title]->setLinkAttributes([
            'class' => 'btn btn-info btn-sm',
        ]);
        if ('household_show' === $route) {
            $menu->addChild('Edit household', [
                'route' => 'household_edit',
                'routeParameters' => array('id' => $id),
            ]);
            $menu['Edit household']->setLinkAttributes([
                'class' => 'btn btn-info btn-sm',
            ]);
        }

        return $menu;
    }

    public function returnFromContacts(array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $uri = $request->headers->get('referer');
        $menu = $this->factory->createItem('root');
        $title = 'return';
        $menu->addChild($title, [
            'label' => 'Return to household',
            'uri' => $uri,
        ]);
        $menu[$title]->setLinkAttributes([
            'class' => 'btn btn-info btn-sm',
        ]);

        return $menu;
    }

    public function profileMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Employment', array(
            'route' => 'employment_profile',
        ));
        $menu->addChild('Housing', array(
            'route' => 'housing_profile',
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

    public function reportsMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('General Statistics', array(
            'route' => 'stats_general',
        ));

        $menu->addChild('Distribution details', array(
            'route' => 'stats_details',
        ));

        $menu->addChild('Most recent contacts (PDF)', [
            'route' => 'latest_contacts',
            'routeParameters' => ['source' => 'Most recent'],
        ]);

        $menu->addChild('Contacts FY to date (PDF)', [
            'route' => 'latest_contacts',
            'routeParameters' => ['source' => 'FY to date'],
        ]);

        $menu->addChild('Annual Turkey (CY, PDF)',[
            'route' => 'annual_turkey',]
            );

        $menu->addChild('Multiple contacts', array(
            'route' => 'multi_contacts',
        ));

        return $menu;
    }
    
    public function databaseMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Tables & their relationships', array(
            'route' => 'db_tables',
        ));

        $menu->addChild('Option tables', array(
            'route' => 'db_options',
        ));

        $menu->addChild('Ad hoc reporting', array(
            'route' => 'db_queries',
        ));

        return $menu;
    }

    public function logoutMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Log out', array(
            'route' => 'logout',
        ));

        return $menu;
    }
}
