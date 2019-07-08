<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerProviderInterface
{
    /**
     * Will provide a valid customer object based on the given email
     */
    public function provide(OrderInterface $order): CustomerInterface;
}
