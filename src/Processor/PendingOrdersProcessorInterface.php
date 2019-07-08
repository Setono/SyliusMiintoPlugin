<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Psr\Log\LoggerAwareInterface;

interface PendingOrdersProcessorInterface extends LoggerAwareInterface
{
    /**
     * @param int $limit Limit the number of orders to process. 0 means process all
     */
    public function process(int $limit = 0): void;
}
