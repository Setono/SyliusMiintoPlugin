<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

class Order extends AbstractResource implements OrderInterface
{
    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @var string
     */
    protected $providerId;

    /**
     * @var string
     */
    protected $status = self::STATUS_PENDING;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}