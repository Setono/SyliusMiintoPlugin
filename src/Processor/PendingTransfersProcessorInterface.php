<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Psr\Log\LoggerAwareInterface;

interface PendingTransfersProcessorInterface extends LoggerAwareInterface
{
    public function process(): void;
}
