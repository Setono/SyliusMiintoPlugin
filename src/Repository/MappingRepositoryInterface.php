<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MappingRepositoryInterface extends RepositoryInterface
{
    public function findMappedByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface;

    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface;

    /**
     * Returns true if a shop, provider id, shipping method and payment method combination exists for the given shop and provider id
     *
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return bool
     */
    public function hasMapping(ShopInterface $shop, string $providerId): bool;
}
