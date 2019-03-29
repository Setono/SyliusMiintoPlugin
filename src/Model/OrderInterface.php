<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderInterface extends ResourceInterface, MutableIdInterface
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_ERRORED = 'errored';

    public function getShop(): ?ShopInterface;

    public function setShop(ShopInterface $shop): void;

    public function getProviderId(): ?string;

    public function setProviderId(string $providerId): void;

    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getOrder(): ?SyliusOrderInterface;

    public function setOrder(SyliusOrderInterface $order): void;

    public function getErrors(): Collection;

    public function addError(OrderErrorInterface $error): void;

    public function setData(array $data): void;

    public function getData(): array;
}
