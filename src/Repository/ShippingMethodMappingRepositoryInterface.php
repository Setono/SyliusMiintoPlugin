<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\ShippingMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ShippingMethodMappingRepositoryInterface extends RepositoryInterface
{
    public function findMappedByShopAndProviderId(ShopInterface $shop, string $providerId): ?ShippingMethodMappingInterface;

    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?ShippingMethodMappingInterface;

    /**
     * Returns true if a shop and provider id combination exists
     *
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return bool
     */
    public function hasMapping(ShopInterface $shop, string $providerId): bool;

    /**
     * Returns true if a shop, provider id, and shipping method combination exists
     *
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return bool
     */
    public function hasValidMapping(ShopInterface $shop, string $providerId): bool;
}
