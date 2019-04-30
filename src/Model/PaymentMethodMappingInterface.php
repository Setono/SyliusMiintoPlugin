<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface PaymentMethodMappingInterface extends ResourceInterface
{
    public function getShop(): ?ShopInterface;

    public function setShop(ShopInterface $shop): void;

    public function getPaymentMethod(): ?PaymentMethodInterface;

    public function setPaymentMethod(PaymentMethodInterface $paymentMethod): void;
}
