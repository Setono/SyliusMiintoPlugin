<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Repository;

use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MappingRepositoryInterface extends RepositoryInterface
{
    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface;
}
