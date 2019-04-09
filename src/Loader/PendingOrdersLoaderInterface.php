<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

use Psr\Log\LoggerAwareInterface;

interface PendingOrdersLoaderInterface extends LoggerAwareInterface
{
    public function load(): void;
}
