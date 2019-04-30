<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ShippingTypeMappingInterface extends ResourceInterface
{
    public function getShop(): ?ShopInterface;

    public function setShop(ShopInterface $shop): void;

    public function getType(): ?string;

    public function setType(string $type): void;

    public function getShippingMethod(): ?ShippingMethodInterface;

    public function setShippingMethod(ShippingMethodInterface $shippingMethod): void;
}
