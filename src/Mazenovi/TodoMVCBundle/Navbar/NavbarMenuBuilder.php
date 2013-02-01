<?php

namespace Mazenovi\TodoMVCBundle\Navbar;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;

class NavbarMenuBuilder extends AbstractNavbarMenuBuilder
{
    protected $securityContext;
    protected $isLoggedIn;

    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext)
    {
        parent::__construct($factory);

        $this->securityContext = $securityContext;
        $this->isLoggedIn = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');
		return $menu;
    }

    public function createRightSideDropdownMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav pull-right');
        
        $this->addDivider($menu, true);

        if ($this->isLoggedIn) {
            $dropdown = $this->createDropdownMenuItem($menu, "Profile", true, array('icon' => 'icon-caret-down'));
            $dropdown->addChild('My Todos', array('route' => 'mazenovi_todomvc_api_listuser', 'routeParameters' => array('id' => $this->securityContext->getToken()->getUser()->getId())));
            $dropdown->addChild('My account', array('route' => 'fos_user_profile_edit'));
            $this->addDivider($menu, true);
            $menu->addChild('logout', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('login', array('route' => 'fos_user_security_login'));
            $this->addDivider($menu, true);
            $menu->addChild('register', array('route' => 'fos_user_registration_register'));
        }

        $this->addDivider($menu, true);

        return $menu;
    }
}
