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

    public function getShippingType(): string;

    /**
     * Returns true if the status equals $status
     *
     * @param string $status
     *
     * @return bool
     */
    public function isStatus(string $status): bool;

    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getOrder(): ?SyliusOrderInterface;

    public function setOrder(SyliusOrderInterface $order): void;

    public function clearErrors(): void;

    /**
     * @return Collection|ErrorInterface[]
     */
    public function getErrors(): Collection;

    public function addError(ErrorInterface $error): void;

    public function hasError(ErrorInterface $error): bool;

    public function setData(array $data): void;

    public function getData(): array;

    public static function getStatuses(): array;
}
