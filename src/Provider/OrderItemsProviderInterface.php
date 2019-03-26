<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItems\OrderItemsInterface;

interface OrderItemsProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderItemsInterface
     */
    public function provide(OrderInterface $order): OrderItemsInterface;
}
