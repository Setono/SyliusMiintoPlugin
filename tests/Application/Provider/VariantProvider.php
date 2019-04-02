<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMiintoPlugin\Application\Provider;

use Setono\SyliusMiintoPlugin\Provider\VariantProviderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

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
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy([]);

        return $productVariant;
    }
}
