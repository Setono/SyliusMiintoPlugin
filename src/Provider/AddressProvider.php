<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;

abstract class AddressProvider implements AddressProviderInterface
{
    /**
     * @var AddressFactoryInterface
     */
    private $addressFactory;

    public function __construct(AddressFactoryInterface $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

    protected function _provide(string $name, array $address): AddressInterface
    {
        $names = explode(' ', $name, 2);

        /** @var AddressInterface $obj */
        $obj = $this->addressFactory->createNew();

        $obj->setFirstName($names[0]);
        $obj->setLastName($names[1] ?? null);
        $obj->setStreet($address['street']);
        $obj->setPostcode($address['postcode']);
        $obj->setCity($address['city']);
        $obj->setCountryCode($address['countryCode']);

        return $obj;
    }
}
