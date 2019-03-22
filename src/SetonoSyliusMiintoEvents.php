<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin;

final class SetonoSyliusMiintoEvents
{
    public const ORDER_LOADER_PRE_PERSIST = 'setono_sylius_miinto.loader.order.pre_persist';
    public const ORDER_LOADER_POST_PERSIST = 'setono_sylius_miinto.loader.order.post_persist';
    public const ORDER_LOADER_POST_FLUSH = 'setono_sylius_miinto.loader.order.post_flush';

    private function __construct()
    {
    }
}
