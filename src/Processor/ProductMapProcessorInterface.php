<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Psr\Log\LoggerAwareInterface;

interface ProductMapProcessorInterface extends LoggerAwareInterface
{
    public function process(): void;
}
