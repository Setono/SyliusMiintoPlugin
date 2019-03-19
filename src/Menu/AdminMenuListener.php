<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $miintoItem = $menu->addChild('miinto')
            ->setLabel('setono_sylius_miinto.ui.miinto');

        $miintoItem->addChild('shops', [
            'route' => ''
        ])
            ->setLabel('setono_sylius_miinto.ui.shops')
            ->setLabelAttribute('icon', 'building')
        ;

        $miintoItem->addChild('orders', [
            'route' => ''
        ])
            ->setLabel('setono_sylius_miinto.ui.orders')
            ->setLabelAttribute('icon', 'cart')
        ;
    }
}
