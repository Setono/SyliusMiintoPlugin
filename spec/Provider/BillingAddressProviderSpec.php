<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMiintoPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Provider\BillingAddressProvider;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;

class BillingAddressProviderSpec extends ObjectBehavior
{
    public function let(AddressFactoryInterface $addressFactory, AddressInterface $address): void
    {
        $addressFactory->createNew()->willReturn($address);

        $this->beConstructedWith($addressFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BillingAddressProvider::class);
    }

    public function it_provides(OrderInterface $order): void
    {
        $order->getData()->willReturn([
            'billingInformation' => [
                'name' => 'John Doe',
                'address' => [
                    'street' => 'Big Street 19',
                    'postcode' => '73450',
                    'city' => 'Great City',
                    'countryCode' => 'DK',
                ],
            ],
        ]);

        $address = $this->provide($order);
        $address->shouldBeAnInstanceOf(AddressInterface::class);
    }
}
