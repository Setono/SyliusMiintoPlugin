<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class MappingRepository extends EntityRepository implements MappingRepositoryInterface
{
    /**
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return MappingInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findMappedByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface
    {
        return $this->shopAndProviderIdQueryBuilder($shop, $providerId)
            ->addSelect('s')
            ->innerJoin('o.shippingMethod', 's')
            ->innerJoin('o.paymentMethod', 'p')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return MappingInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface
    {
        return $this->shopAndProviderIdQueryBuilder($shop, $providerId)->getQuery()->getOneOrNullResult();
    }

    /**
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function hasMapping(ShopInterface $shop, string $providerId): bool
    {
        return (int) $this->shopAndProviderIdQueryBuilder($shop, $providerId)
            ->select('COUNT(o)')
            ->andWhere('o.shippingMethod is not null')
            ->andWhere('o.paymentMethod is not null')
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
