<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMiintoPlugin\Application\Mapper;

use Setono\SyliusMiintoPlugin\Exception\NoMappingFoundException;
use Setono\SyliusMiintoPlugin\Mapper\ProductVariantMapperInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantMapper implements ProductVariantMapperInterface
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function map(array $item): ProductVariantInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy([]);
        if (!$productVariant instanceof ProductVariantInterface) {
            throw new NoMappingFoundException($item);
        }

        return $productVariant;
    }
}
