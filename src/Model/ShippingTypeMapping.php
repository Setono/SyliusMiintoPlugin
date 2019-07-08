<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\ShippingMethodInterface;

class ShippingTypeMapping implements ShippingTypeMappingInterface
{
    /** @var int */
    protected $id;

    /** @var ShopInterface */
    protected $shop;

    /** @var string */
    protected $type;

    /** @var ShippingMethodInterface */
    protected $shippingMethod;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getShippingMethod(): ?ShippingMethodInterface
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }
}
