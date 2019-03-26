<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

class Mapping implements MappingInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @var string
     */
    protected $providerId;

    /**
     * @var ShippingMethodInterface
     */
    protected $shippingMethod;

    /**
     * @var PaymentMethodInterface
     */
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

    public function getPaymentMethod(): ?PaymentMethodInterface
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }
}
