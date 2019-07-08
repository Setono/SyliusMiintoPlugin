<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Message\Command;

/**
 * This command loads a Miinto order into the local database
 */
final class LoadOrder implements CommandInterface
{
    /** @var string */
    private $shopId;

    /** @var int */
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
