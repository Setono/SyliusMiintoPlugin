<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Mapper;

use Setono\SyliusMiintoPlugin\Exception\NoMappingFoundException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantMapper implements ProductVariantMapperInterface
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var string */
    private $gtinField;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository, string $gtinField)
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->gtinField = $gtinField;
    }

    public function map(array $item): ProductVariantInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy([
            $this->gtinField => $item['gtin'],
        ]);

        if (null === $productVariant) {
            throw new NoMappingFoundException($item);
        }

        return $productVariant;
    }
}
