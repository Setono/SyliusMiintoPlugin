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
            'route' => 'setono_sylius_miinto_admin_shop_index'
        ])
            ->setLabel('setono_sylius_miinto.ui.shops')
            ->setLabelAttribute('icon', 'building outline')
        ;

        $miintoItem->addChild('orders', [
            'route' => 'setono_sylius_miinto_admin_order_index'
        ])
            ->setLabel('setono_sylius_miinto.ui.orders')
            ->setLabelAttribute('icon', 'cart')
        ;

        $miintoItem->addChild('mappings', [
            'route' => 'setono_sylius_miinto_admin_mapping_index'
        ])
            ->setLabel('setono_sylius_miinto.ui.mappings')
            ->setLabelAttribute('icon', 'arrow alternate circle right')
        ;
    }
}
