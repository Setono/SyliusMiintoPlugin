<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

// todo refactor to error interface instead
interface OrderErrorInterface extends BaseResourceInterface, TimestampableInterface
{
    public function getOrder(): ?OrderInterface;

    public function setOrder(OrderInterface $order): void;

    public function getMessage(): ?string;

    public function setMessage(string $message): void;
}
