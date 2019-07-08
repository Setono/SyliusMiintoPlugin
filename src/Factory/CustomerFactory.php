<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerFactory implements CustomerFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var RepositoryInterface */
    private $customerGroupRepository;

    /** @var CustomerGroupFactoryInterface */
    private $customerGroupFactory;

    public function __construct(
        FactoryInterface $decoratedFactory,
        RepositoryInterface $customerGroupRepository,
        CustomerGroupFactoryInterface $customerGroupFactory
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerGroupFactory = $customerGroupFactory;
    }

    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    public function createFromOrder(OrderInterface $order): CustomerInterface
    {
        /** @var CustomerGroupInterface|null $group */
        $group = $this->customerGroupRepository->findOneBy([
            'code' => CustomerGroupFactoryInterface::MIINTO_GROUP_CODE,
        ]);
        if (null === $group) {
            $group = $this->customerGroupFactory->createMiintoGroup();
            $this->customerGroupRepository->add($group);
        }

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
        $customer->setGroup($group);

        return $customer;
    }
}
