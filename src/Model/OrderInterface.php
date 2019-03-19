<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

interface OrderInterface extends ResourceInterface
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_ERRORED = 'errored';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     */
    public function setStatus(string $status): void;
}
