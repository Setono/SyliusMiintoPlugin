<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Resolver;

use Setono\SyliusMiintoPlugin\Mapper\ProductVariantMapperInterface;
use Setono\SyliusMiintoPlugin\Position\Positions;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class PositionResolver implements PositionResolverInterface
{
    /** @var ProductVariantMapperInterface */
    private $productVariantMapper;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        ProductVariantMapperInterface $productVariantMapper,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->productVariantMapper = $productVariantMapper;
        $this->availabilityChecker = $availabilityChecker;
    }

    /*
     * todo A price field is present on a pending position. This is the price you need to adhere to. Therefore it would be wise to check if that price is the same as our price or at least within a given threshold of our price
     */
    public function resolve(array $pendingPositions): Positions
    {
        $accepted = $declined = [];

        foreach ($pendingPositions as $pendingPosition) {
            $productVariant = $this->productVariantMapper->map($pendingPosition['item']);
            if ($this->availabilityChecker->isStockSufficient($productVariant, $pendingPosition['quantity'])) {
                $accepted[] = $pendingPosition['id'];
            } else {
                $declined[] = $pendingPosition['id'];
            }
        }

        return new Positions($accepted, $declined);
    }
}
