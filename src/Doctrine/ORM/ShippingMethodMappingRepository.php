<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMiintoPlugin\Model\ShippingMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingMethodMappingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class ShippingMethodMappingRepository extends EntityRepository implements ShippingMethodMappingRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     */
    public function findMappedByShopAndProviderId(ShopInterface $shop, string $providerId): ?ShippingMethodMappingInterface
    {
        return $this->shopAndProviderIdQueryBuilder($shop, $providerId)
            ->addSelect('s')
            ->innerJoin('o.shippingMethod', 's')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?ShippingMethodMappingInterface
    {
        return $this->shopAndProviderIdQueryBuilder($shop, $providerId)->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function hasMapping(ShopInterface $shop, string $providerId): bool
    {
        return (int) $this->shopAndProviderIdQueryBuilder($shop, $providerId)
            ->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function hasValidMapping(ShopInterface $shop, string $providerId): bool
    {
        return (int) $this->shopAndProviderIdQueryBuilder($shop, $providerId)
                ->select('COUNT(o)')
                ->andWhere('o.shippingMethod is not null')
                ->getQuery()
                ->getSingleScalarResult() > 0
            ;
    }

    private function shopAndProviderIdQueryBuilder(ShopInterface $shop, string $providerId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.shop = :shop')
            ->andWhere('o.providerId = :providerId')
            ->setParameter('shop', $shop)
            ->setParameter('providerId', $providerId)
        ;
    }
}
