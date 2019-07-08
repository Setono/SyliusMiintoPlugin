<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Factory\CustomerFactoryInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

final class CustomerProvider implements CustomerProviderInterface
{
    /** @var CustomerFactoryInterface */
    private $customerFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(CustomerFactoryInterface $customerFactory, CustomerRepositoryInterface $customerRepository)
    {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    public function provide(OrderInterface $order): CustomerInterface
    {
        $data = $order->getData();
        $email = $data['billingInformation']['email'];

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy([
            'email' => $email,
        ]);

        if (null === $customer) {
            $customer = $this->customerFactory->createFromOrder($order);
        }

        $names = [];
        if (isset($data['billingInformation']['name'])) {
            $names = explode(' ', $data['billingInformation']['name'], 2);
        }

        $customer->setFirstName($names[0] ?? null);
        $customer->setLastName($names[1] ?? null);
        $customer->setPhoneNumber($data['billingInformation']['phone'] ?? null);

        return $customer;
    }
}
