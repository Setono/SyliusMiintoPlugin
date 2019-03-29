<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderFulfiller;

use Sylius\Component\Core\Model\OrderInterface;

final class OrderFulfillment
{
    /**
     * @var int[]
     */
    private $fulfillablePositionIds;

    /**
     * @var int[]
     */
    private $unfulfillablePositionIds;

    /**
     * @var OrderInterface|null
     */
    private $order;

    public function __construct(array $fulfillablePositionIds, array $unfulfillablePositionIds, OrderInterface $order = null)
    {
        $this->fulfillablePositionIds = $fulfillablePositionIds;
        $this->unfulfillablePositionIds = $unfulfillablePositionIds;
        $this->order = $order;
    }

    public function getFulfillablePositionIds(): array
    {
        return $this->fulfillablePositionIds;
    }

    public function getUnfulfillablePositionIds(): array
    {
        return $this->unfulfillablePositionIds;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }
}
