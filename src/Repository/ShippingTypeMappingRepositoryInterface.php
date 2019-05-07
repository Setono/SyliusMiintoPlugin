<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\ShippingTypeMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ShippingTypeMappingRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns true if a shop and shipping type combination exists
     *
     * @param ShopInterface $shop
     * @param string $shippingType
     *
     * @return bool
     */
    public function hasMapping(ShopInterface $shop, string $shippingType): bool;

    /**
     * Returns true if a shop, shipping type, and shipping method combination exists
     *
     * @param ShopInterface $shop
     * @param string $shippingType
     *
     * @return bool
     */
    public function hasValidMapping(ShopInterface $shop, string $shippingType): bool;

    /**
     * Returns a shipping type mapping where the shop, shipping type, and shipping method combination is set
     *
     * @param ShopInterface $shop
     * @param string $shippingType
     *
     * @return ShippingTypeMappingInterface|null
     */
    public function findOneValid(ShopInterface $shop, string $shippingType): ?ShippingTypeMappingInterface;
}
