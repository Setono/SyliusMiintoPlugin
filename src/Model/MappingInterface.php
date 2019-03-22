<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface MappingInterface extends BaseResourceInterface
{
    /**
     * @return ShopInterface|null
     */
    public function getShop(): ?ShopInterface;

    /**
     * @param ShopInterface $shop
     */
    public function setShop(ShopInterface $shop): void;

    /**
     * This is the provider id from Miintos API
     *
     * @return string|null
     */
    public function getProviderId(): ?string;

    /**
     * @param string $providerId
     */
    public function setProviderId(string $providerId): void;

    /**
     * @return ShippingMethodInterface|null
     */
    public function getShippingMethod(): ?ShippingMethodInterface;

    /**
     * @param ShippingMethodInterface $shippingMethod
     */
    public function setShippingMethod(ShippingMethodInterface $shippingMethod): void;
}
