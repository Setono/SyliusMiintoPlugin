<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerFactoryInterface extends FactoryInterface
{
    public function createFromOrder(OrderInterface $order): CustomerInterface;
}
