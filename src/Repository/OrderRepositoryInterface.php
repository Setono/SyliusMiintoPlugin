<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $limit Limit the number of orders to find. 0 means find all
     *
     * @return OrderInterface[]
     */
    public function findPending(int $limit = 0): array;
}
