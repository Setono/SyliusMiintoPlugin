<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMiintoPlugin\Model\PaymentMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class PaymentMethodMappingRepository extends EntityRepository implements PaymentMethodMappingRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function findOneValid(ShopInterface $shop): ?PaymentMethodMappingInterface
    {
        return $this->shopQueryBuilder($shop)
                ->andWhere('o.paymentMethod is not null')
                ->getQuery()
                ->getOneOrNullResult()
            ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function hasMapping(ShopInterface $shop): bool
    {
        return (int) $this->shopQueryBuilder($shop)
                ->select('COUNT(o)')
                ->getQuery()
                ->getSingleScalarResult() > 0
            ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function hasValidMapping(ShopInterface $shop): bool
    {
        return (int) $this->shopQueryBuilder($shop)
                ->select('COUNT(o)')
                ->andWhere('o.paymentMethod is not null')
                ->getQuery()
                ->getSingleScalarResult() > 0
            ;
    }

    private function shopQueryBuilder(ShopInterface $shop): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.shop = :shop')
            ->setParameter('shop', $shop)
        ;
    }
}
