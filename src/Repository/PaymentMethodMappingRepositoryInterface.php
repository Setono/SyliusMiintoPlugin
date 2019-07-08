<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\PaymentMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PaymentMethodMappingRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns a mapping where the payment method is set
     */
    public function findOneValid(ShopInterface $shop): ?PaymentMethodMappingInterface;

    /**
     * Returns true if a mapping for the given shop exists
     */
    public function hasMapping(ShopInterface $shop): bool;

    /**
     * Returns true if a shop and payment method combination exists
     */
    public function hasValidMapping(ShopInterface $shop): bool;
}
