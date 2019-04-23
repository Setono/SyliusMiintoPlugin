<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

interface OrderLoaderInterface
{
    public function load(string $shopId, int $orderId): void;
}
