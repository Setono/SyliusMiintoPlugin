<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

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
}
