<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItems\OrderItems;

interface OrderItemsProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderItems
     */
    public function provide(OrderInterface $order): OrderItems;
}
