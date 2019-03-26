<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\OrderRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{
    public function findPending(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', OrderInterface::STATUS_PENDING)
            ->getQuery()
            ->getResult()
        ;
    }
}
