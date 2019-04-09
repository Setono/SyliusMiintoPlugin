<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

use Setono\SyliusMiintoPlugin\Position\Positions;
use Setono\SyliusMiintoPlugin\ProductMap\ProductMapInterface;

interface ClientInterface
{
    /**
     * Returns an array of shop ids
     *
     * @return array
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
     * Returns an array of orders for the given shop id
     *
     * @param string $shopId
     * @param array $options
     *
     * @return array
     */
    public function getOrders(string $shopId, array $options = []): array;

    /**
     * Accepts or declines the given positions on the given order
     *
     * @param string $shopId
     * @param int $orderId
     * @param array $acceptedPositions
     * @param array $declinedPositions
     */
    public function updateOrder(string $shopId, int $orderId, array $acceptedPositions = [], array $declinedPositions = []): void;

    /**
     * Returns an array of transfers for the given shop id
     *
     * @param string $shopId
     * @param array $options
     * @return array
     */
    public function getTransfers(string $shopId, array $options = []): array;

    /**
     * Accepts or declines the given positions on the given order
     *
     * @param string $shopId
     * @param int $transferId
     * @param Positions $positions
     */
    public function updateTransfer(string $shopId, int $transferId, Positions $positions): void;

    /**
     * Fetches the product map from Miinto and returns an instance of ProductMapInterface which can then be traversed
     *
     * @param string $shopId
     * @return ProductMapInterface
     */
    public function getProductMap(string $shopId): ProductMapInterface;
}
