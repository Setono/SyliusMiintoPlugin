<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Message\Command;

final class LoadOrder
{
    /**
     * @var string
     */
    private $shopId;

    /**
     * @var int
     */
    private $orderId;

    public function __construct(string $shopId, int $orderId)
    {
        $this->shopId = $shopId;
        $this->orderId = $orderId;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
