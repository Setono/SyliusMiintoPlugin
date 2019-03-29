<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface OrderErrorInterface extends BaseResourceInterface, TimestampableInterface
{
    public function getOrder(): ?OrderInterface;

    public function setOrder(OrderInterface $order): void;

    public function getMessage(): ?string;

    public function setMessage(string $message): void;
}
