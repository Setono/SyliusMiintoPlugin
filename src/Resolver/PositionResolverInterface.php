<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Resolver;

use Setono\SyliusMiintoPlugin\Position\Positions;

interface PositionResolverInterface
{
    /**
     * Receives an array of pending positions and returns an object
     * containing positions we can either accept or decline based on our stock
     */
    public function resolve(array $pendingPositions): Positions;
}
