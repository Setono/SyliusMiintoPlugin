<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

/**
 * todo FIX THIS CLASS
 */
final class VariantProvider implements VariantProviderInterface
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function provide(array $item): ProductVariantInterface
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy([]);

        return $productVariant;
    }
}
