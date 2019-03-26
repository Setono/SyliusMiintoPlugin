<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\AddressInterface;

interface AddressProviderInterface
{
    /**
     * Will provide a valid address object based on the given order
     *
     * @param OrderInterface $order
     *
     * @return AddressInterface
     */
    public function provide(OrderInterface $order): AddressInterface;
}
