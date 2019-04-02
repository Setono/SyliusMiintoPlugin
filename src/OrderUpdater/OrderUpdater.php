<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderUpdater;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\OrderFulfiller\OrderFulfillment;

final class OrderUpdater implements OrderUpdaterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param OrderFulfillment $orderFulfillment
     *
     * @throws StringsException
     */
    public function update(OrderFulfillment $orderFulfillment): void
    {
        $shop = $orderFulfillment->getOrder()->getShop();
        if (null === $shop) {
            throw new InvalidArgumentException(\Safe\sprintf('Shop is not set on order %s', $orderFulfillment->getOrder()->getId()));
        }

        $this->client->updateOrder($shop->getId(), $orderFulfillment->getOrder()->getId(), $orderFulfillment->getFulfillablePositionIds(), $orderFulfillment->getUnfulfillablePositionIds());
    }
}
