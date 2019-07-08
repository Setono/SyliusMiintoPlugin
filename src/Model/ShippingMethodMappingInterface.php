<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface ShippingMethodMappingInterface extends BaseResourceInterface
{
    public function getShop(): ?ShopInterface;

    public function setShop(ShopInterface $shop): void;

    /**
     * This is the provider id from Miintos API
     */
    public function getProviderId(): ?string;

    public function setProviderId(string $providerId): void;

    public function getShippingMethod(): ?ShippingMethodInterface;

    public function setShippingMethod(ShippingMethodInterface $shippingMethod): void;

    public function isDefault(): bool;

    public function setDefault(bool $default): void;
}
