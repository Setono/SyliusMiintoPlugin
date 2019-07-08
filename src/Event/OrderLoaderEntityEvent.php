<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Event;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class OrderLoaderEntityEvent extends Event
{
    /** @var OrderInterface */
    private $order;

    /** @var string */
    private $shopId;

    public function __construct(OrderInterface $order, string $shopId)
    {
        $this->order = $order;
        $this->shopId = $shopId;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }
}
