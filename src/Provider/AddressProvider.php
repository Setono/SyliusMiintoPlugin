<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;

abstract class AddressProvider implements AddressProviderInterface
{
    /** @var AddressFactoryInterface */
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

        // We only get an attribute named 'name' from Miinto, but in Sylius we need the last name.
        // Therefore we add the first name as the last name if no last name is set
        $obj->setLastName($names[1] ?? $names[0]);
        $obj->setStreet($address['street'] ?? null);
        $obj->setPostcode($address['postcode'] ?? null);
        $obj->setCity($address['city'] ?? null);
        $obj->setCountryCode($address['countryCode'] ?? null);

        return $obj;
    }
}
