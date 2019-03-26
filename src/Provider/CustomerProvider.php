<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Factory\CustomerFactoryInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

final class CustomerProvider implements CustomerProviderInterface
{
    /**
     * @var CustomerFactoryInterface
     */
    private $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
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

        return $customer;
    }
}
