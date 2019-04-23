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
     *
     * @param string $shopId
     *
     * @return array
     */
    public function getShopDetails(string $shopId): array;

    /**
     * Returns order details for the given shop id and order id
     *
     * @param string $shopId
     * @param int $orderId
     *
     * @return array
     */
    public function getOrder(string $shopId, int $orderId): array;

    /**
     * Returns an array of transfers for the given shop id
     *
     * @param string $shopId
     * @param array $options
     *
     * @return array
     */
    public function getTransfers(string $shopId, array $options = []): array;

    /**
     * Accepts or declines the given positions on the given order
     * Returns the order id from order created by any accepted positions. If no positions were accepted it returns null
     *
     * @param string $shopId
     * @param int $transferId
     * @param Positions $positions
     *
     * @return int|null
     */
    public function updateTransfer(string $shopId, int $transferId, Positions $positions): ?int;
}
