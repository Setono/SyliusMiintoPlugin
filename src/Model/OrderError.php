<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class OrderError implements OrderErrorInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var OrderInterface|null
     */
    protected $order;

    /**
     * @var string
     */
    protected $message;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
