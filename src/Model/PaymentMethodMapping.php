<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentMethodMapping implements PaymentMethodMappingInterface
{
    /** @var int */
    protected $id;

    /** @var ShopInterface */
    protected $shop;

    /** @var PaymentMethodInterface */
    protected $paymentMethod;

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

    public function getPaymentMethod(): ?PaymentMethodInterface
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }
}
