<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface ShipmentProviderInterface
{
    /**
     * Returns a valid shipment object based on the given order
     *
     * @param OrderInterface $order
     *
     * @return ShipmentInterface
     */
    public function provide(OrderInterface $order): ShipmentInterface;
}
