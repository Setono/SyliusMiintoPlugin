<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderUpdater;

use Setono\SyliusMiintoPlugin\OrderFulfiller\OrderFulfillment;

interface OrderUpdaterInterface
{
    /**
     * Updates Miinto order with given positions
     *
     * @param OrderFulfillment $orderFulfillment
     */
    public function update(OrderFulfillment $orderFulfillment): void;
}
