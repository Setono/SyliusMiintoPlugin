<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerFactory implements CustomerFactoryInterface
{
    private $decoratedFactory;

    public function __construct(FactoryInterface $factory)
    {
        $this->decoratedFactory = $factory;
    }

    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    public function createFromOrder(OrderInterface $order): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->decoratedFactory->createNew();

        $data = $order->getData();

        $customer->setEmail($data['billingInformation']['email']);

        // todo set default address

        return $customer;
    }
}
