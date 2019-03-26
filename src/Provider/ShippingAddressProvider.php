<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\AddressInterface;

final class ShippingAddressProvider extends AddressProvider
{
    public function provide(OrderInterface $order): AddressInterface
    {
        $data = $order->getData();

        return $this->_provide($data['shippingInformation']['deliveryAddress']['name'], $data['shippingInformation']['deliveryAddress']['address']);
    }
}
