<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface VariantProviderInterface
{
    /**
     * Returns a product variant from the given item which is an item array from Miinto
     *
     * The item is located in order -> pendingPositions -> item
     *
     * @param array $item
     *
     * @return ProductVariantInterface
     */
    public function provide(array $item): ProductVariantInterface;
}
