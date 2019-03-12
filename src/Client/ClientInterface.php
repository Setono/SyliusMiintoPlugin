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
     * Returns an array of orders for the given shop id
     *
     * @param int $shopId
     * @param array $options
     * @return array
     */
    public function getOrders(int $shopId, array $options = []): array;
}
