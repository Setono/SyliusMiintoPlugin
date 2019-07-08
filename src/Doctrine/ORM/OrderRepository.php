<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Doctrine\ORM;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\OrderRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{
    public function findPending(int $limit = 0): array
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', OrderInterface::STATUS_PENDING)
        ;

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
