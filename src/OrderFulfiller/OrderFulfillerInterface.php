<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderFulfiller;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;

interface OrderFulfillerInterface
{
    public function fulfill(OrderInterface $order): void;
}
