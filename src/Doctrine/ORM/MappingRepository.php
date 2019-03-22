<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class MappingRepository extends EntityRepository implements MappingRepositoryInterface
{
    /**
     * @param ShopInterface $shop
     * @param string $providerId
     * @return MappingInterface|null
     * @throws NonUniqueResultException
     */
    public function findOneByShopAndProviderId(ShopInterface $shop, string $providerId): ?MappingInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.shop = :shop')
            ->andWhere('.providerId = :providerId')
            ->setParameter('shop', $shop)
            ->setParameter('providerId', $providerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
