<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

interface PendingOrdersLoaderInterface
{
    public function load(): void;
}
