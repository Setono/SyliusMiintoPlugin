<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Event;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Symfony\Component\EventDispatcher\Event;

final class OrderEvent extends Event
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var string
     */
    private $shopId;

    public function __construct(OrderInterface $order, string $shopId)
    {
        $this->order = $order;
        $this->shopId = $shopId;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }
}
