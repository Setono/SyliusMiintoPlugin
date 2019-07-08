<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerGroupFactoryInterface extends FactoryInterface
{
    public const MIINTO_GROUP_CODE = 'miinto';

    public function createMiintoGroup(): CustomerGroupInterface;
}
