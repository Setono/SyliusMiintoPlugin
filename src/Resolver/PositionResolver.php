<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Resolver;

use Setono\SyliusMiintoPlugin\Mapper\ProductVariantMapperInterface;
use Setono\SyliusMiintoPlugin\Position\Positions;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class PositionResolver implements PositionResolverInterface
{
    /**
     * @var ProductVariantMapperInterface
     */
    private $productVariantMapper;

    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    public function __construct(ProductVariantMapperInterface $productVariantMapper, AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->productVariantMapper = $productVariantMapper;
        $this->availabilityChecker = $availabilityChecker;
    }

    /*
     * todo implement something that checks the price
     */
    public function resolve(array $pendingPositions): Positions
    {
        $accepted = $declined = [];

        foreach ($pendingPositions as $pendingPosition) {
            $productVariant = $this->productVariantMapper->map($pendingPosition['item']);
            if($this->availabilityChecker->isStockSufficient($productVariant, $pendingPosition['quantity'])) {
                $accepted[] = $pendingPosition['id'];
            } else {
                $declined[] = $pendingPosition['id'];
            }
        }

        return new Positions($accepted, $declined);
    }
}
