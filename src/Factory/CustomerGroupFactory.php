<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerGroupFactory implements CustomerGroupFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    public function createMiintoGroup(): CustomerGroupInterface
    {
        /** @var CustomerGroupInterface $customerGroup */
        $customerGroup = $this->decoratedFactory->createNew();
        $customerGroup->setCode(self::MIINTO_GROUP_CODE);
        $customerGroup->setName('Miinto');

        return $customerGroup;
    }
}
