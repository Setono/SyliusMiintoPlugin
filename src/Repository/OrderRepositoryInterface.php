<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @return OrderInterface[]
     */
    public function findPending(): array;
}
