<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider\OrderItems;

use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderItems implements OrderItemsInterface
{
    /**
     * @var OrderItemInterface[]
     */
    private $orderItems;

    /**
     * @var int[]
     */
    private $fulfillablePositionIds;

    /**
     * @var int[]
     */
    private $unfulfillablePositionIds;

    public function __construct(array $orderItems, array $fulfillablePositionIds, array $unfulfillablePositionIds)
    {
        $this->orderItems = $orderItems;
        $this->fulfillablePositionIds = $fulfillablePositionIds;
        $this->unfulfillablePositionIds = $unfulfillablePositionIds;
    }

    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getFulfillablePositionIds(): array
    {
        return $this->fulfillablePositionIds;
    }

    public function getUnfulfillablePositionIds(): array
    {
        return $this->unfulfillablePositionIds;
    }
}
