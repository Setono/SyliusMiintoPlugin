<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider\OrderItems;

use Sylius\Component\Core\Model\OrderItemInterface;

interface OrderItemsInterface
{
    /**
     * @return OrderItemInterface[]
     */
    public function getOrderItems(): array;

    /**
     * @return int[]
     */
    public function getFulfillablePositionIds(): array;

    /**
     * @return int[]
     */
    public function getUnfulfillablePositionIds(): array;
}
