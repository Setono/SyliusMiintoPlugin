<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class VariantProvider implements VariantProviderInterface
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var string
     */
    private $gtinField;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository, string $gtinField)
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->gtinField = $gtinField;
    }

    /**
     * @param array $item
     *
     * @return ProductVariantInterface
     *
     * @throws StringsException
     * @throws InvalidArgumentException
     */
    public function provide(array $item): ProductVariantInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy([
            $this->gtinField => $item['gtin'],
        ]);

        if (null === $productVariant) {
            throw new InvalidArgumentException(\Safe\sprintf('No product variant matches the GTIN %s', $item['gtin']));
        }

        return $productVariant;
    }
}
