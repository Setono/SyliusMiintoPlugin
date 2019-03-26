<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentProviderInterface
{
    /**
     * Returns a valid payment object based on the given order
     *
     * @param OrderInterface $order
     *
     * @return PaymentInterface
     */
    public function provide(OrderInterface $order): PaymentInterface;
}
