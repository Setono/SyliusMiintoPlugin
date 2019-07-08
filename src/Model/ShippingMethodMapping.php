<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\ShippingMethodInterface;

class ShippingMethodMapping implements ShippingMethodMappingInterface
{
    /** @var int */
    protected $id;

    /** @var ShopInterface */
    protected $shop;

    /** @var string */
    protected $providerId;

    /** @var ShippingMethodInterface */
    protected $shippingMethod;

    /** @var bool */
    protected $default = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShop(): ?ShopInterface
    {
        return $this->shop;
    }

    public function setShop(ShopInterface $shop): void
    {
        $this->shop = $shop;
    }

    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function setProviderId(string $providerId): void
    {
        $this->providerId = $providerId;
    }

    public function getShippingMethod(): ?ShippingMethodInterface
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }
}
