<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

use Setono\SyliusMiintoPlugin\Position\Positions;

interface ClientInterface
{
    /**
     * Returns an array of shop ids
     *
     * @return string[]
     */
    public function getShopIds(): array;

    /**
     * Returns details for the given shop
     */
    public function getShopDetails(string $shopId): array;

    /**
     * Returns order details for the given shop id and order id
     */
    public function getOrder(string $shopId, int $orderId): array;

    /**
     * Returns an array of transfers for the given shop id
     */
    public function getTransfers(string $shopId, array $options = []): array;

    /**
     * Accepts or declines the given positions on the given order
     * Returns the order id from order created by any accepted positions. If no positions were accepted it returns null
     */
    public function updateTransfer(string $shopId, int $transferId, Positions $positions): ?int;

    /**
     * Gets the array of available shipping providers for the given order
     */
    public function getShippingProviders(string $shopId, int $orderId): array;
}
