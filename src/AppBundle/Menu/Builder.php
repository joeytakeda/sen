<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Menu builder for the navigation and search menus.
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    // U+25BE, black down-pointing small triangle.
    const CARET = ' ▾';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Build the menu builder.
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage) {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    private function hasRole($role) {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }
        return $this->authChecker->isGranted($role);
    }

    /**
     * Build the navigation menu and return it.
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));
        $menu->addChild('home', array(
            'label' => 'Home',
            'route' => 'homepage',
        ));

        $browse = $menu->addChild('browse', array(
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ));
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('Public', array(
            'uri' => '#',
        ));

        if ($this->hasRole('ROLE_USER')) {
            $browse->addChild('divider', array(
                'label' => '',
            ));
            $browse->addChild('Private', array(
                'uri' => '#',
            ));
        }

        return $menu;
    }

}
