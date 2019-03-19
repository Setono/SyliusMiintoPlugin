<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

class Order extends AbstractResource implements OrderInterface
{
    /**
     * @var string
     */
    protected $status = self::STATUS_PENDING;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
