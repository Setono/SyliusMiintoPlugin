<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Mapper;

use Setono\SyliusMiintoPlugin\Exception\NoMappingFoundException;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantMapperInterface
{
    /**
     * Maps a given Miinto item to a product variant
     * Throws an exception if no mapping was found
     *
     * @throws NoMappingFoundException
     */
    public function map(array $item): ProductVariantInterface;
}
