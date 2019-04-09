<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Handler;

use Psr\Log\LoggerAwareInterface;

interface PendingTransfersHandlerInterface extends LoggerAwareInterface
{
    public function handle(): void;
}
