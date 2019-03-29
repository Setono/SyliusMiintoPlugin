<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\OrderInterface as ShopOrderInterface;

class Order implements OrderInterface
{
    use MutableIdTrait;

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

    /**
     * @var ShopOrderInterface
     */
    protected $order;

    /**
     * @var Collection|OrderErrorInterface[]
     */
    protected $errors;

    /**
     * @var array
     */
    protected $data = [];

    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    public function getId(): int
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOrder(): ?ShopOrderInterface
    {
        return $this->order;
    }

    public function setOrder(ShopOrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function addError(OrderErrorInterface $error): void
    {
        if(!$this->hasError($error)) {
            $this->errors->add($error);
            $error->setOrder($this);
        }
    }

    public function hasError(OrderErrorInterface $error): bool
    {
        return $this->errors->contains($error);
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
