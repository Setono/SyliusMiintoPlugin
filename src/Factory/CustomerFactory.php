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

        $names = [];
        if (isset($data['billingInformation']['name'])) {
            $names = explode(' ', $data['billingInformation']['name'], 2);
        }

        $customer->setEmail($data['billingInformation']['email']);
        $customer->setFirstName($names[0] ?? null);
        $customer->setLastName($names[1] ?? null);
        $customer->setPhoneNumber($data['billingInformation']['phone'] ?? null);

        return $customer;
    }
}
