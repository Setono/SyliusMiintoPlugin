<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

interface PendingOrdersProcessorInterface
{
    public function process(): void;
}
