<?php

namespace spec\Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Provider\VariantProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

class VariantProviderSpec extends ObjectBehavior
{
    private const GTIN_FIELD = 'gtin';

    public function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository, self::GTIN_FIELD);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantProvider::class);
    }

    public function it_throws_exception(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $productVariantRepository->findOneBy([
            self::GTIN_FIELD => '1234',
        ])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [
            ['gtin' => '1234']
        ]);
    }

    public function it_returns_product_variant(ProductVariantRepositoryInterface $productVariantRepository, ProductVariantInterface $productVariant): void
    {
        $productVariantRepository->findOneBy([
            self::GTIN_FIELD => '1234',
        ])->willReturn($productVariant);

        $this->provide([
            'gtin' => '1234'
        ])->shouldReturn($productVariant);
    }
}
