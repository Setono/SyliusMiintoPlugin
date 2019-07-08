<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMiintoPlugin\Model\ShippingTypeMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingTypeMappingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class ShippingTypeMappingRepository extends EntityRepository implements ShippingTypeMappingRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function findOneValid(ShopInterface $shop, string $shippingType): ?ShippingTypeMappingInterface
    {
        return $this->shopAndShippingTypeQueryBuilder($shop, $shippingType)
            ->andWhere('o.shippingMethod is not null')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function hasMapping(ShopInterface $shop, string $shippingType): bool
    {
        return (int) $this->shopAndShippingTypeQueryBuilder($shop, $shippingType)
            ->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function hasValidMapping(ShopInterface $shop, string $shippingType): bool
    {
        return (int) $this->shopAndShippingTypeQueryBuilder($shop, $shippingType)
                ->select('COUNT(o)')
                ->andWhere('o.shippingMethod is not null')
                ->getQuery()
                ->getSingleScalarResult() > 0
            ;
    }

    private function shopAndShippingTypeQueryBuilder(ShopInterface $shop, string $shippingType): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.shop = :shop')
            ->andWhere('o.type = :type')
            ->setParameter('shop', $shop)
            ->setParameter('type', $shippingType)
        ;
    }
}
