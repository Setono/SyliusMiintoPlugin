<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

use Doctrine\Persistence\ObjectManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderPostFlushEvent;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderPreFlushEvent;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderPrePersistEvent;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderStartedEvent;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class OrderLoader implements OrderLoaderInterface
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ClientInterface */
    private $client;

    /** @var RepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $orderFactory;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClientInterface $client,
        RepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        ObjectManager $orderManager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->client = $client;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderManager = $orderManager;
    }

    public function load(string $shopId, int $orderId): void
    {
        $this->eventDispatcher->dispatch(new OrderLoaderStartedEvent($shopId, $orderId));

        $order = $this->client->getOrder($shopId, $orderId);

        /** @var OrderInterface|null $entity */
        $entity = $this->orderRepository->find($order['id']); // @todo if the entity is found we should handle this, since this is not intended

        if (null !== $entity && !$entity->isStatus(OrderInterface::STATUS_PENDING)) {
            // todo ask Miinto if this can ever happen
            return;
        }

        if (null === $entity) {
            /** @var OrderInterface $entity */
            $entity = $this->orderFactory->createNew();
            $entity->setId($order['id']);
        }

        $entity->setData($order);

        $this->eventDispatcher->dispatch(new OrderLoaderPrePersistEvent($entity, $shopId));

        $this->orderManager->persist($entity);

        $this->eventDispatcher->dispatch(new OrderLoaderPreFlushEvent($entity, $shopId));

        $this->orderManager->flush();

        $this->eventDispatcher->dispatch(new OrderLoaderPostFlushEvent($entity, $shopId));
    }
}
