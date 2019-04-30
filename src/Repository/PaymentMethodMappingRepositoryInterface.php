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
     *
     * @param ShopInterface $shop
     *
     * @return PaymentMethodMappingInterface|null
     */
    public function findOneValid(ShopInterface $shop): ?PaymentMethodMappingInterface;

    /**
     * Returns true if a mapping for the given shop exists
     *
     * @param ShopInterface $shop
     *
     * @return bool
     */
    public function hasMapping(ShopInterface $shop): bool;

    /**
     * Returns true if a shop and payment method combination exists
     *
     * @param ShopInterface $shop
     *
     * @return bool
     */
    public function hasValidMapping(ShopInterface $shop): bool;
}
