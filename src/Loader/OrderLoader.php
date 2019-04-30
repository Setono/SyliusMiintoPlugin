<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderEvent;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderStartedEvent;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderLoader implements OrderLoaderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var ObjectManager
     */
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
        $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_STARTED, new OrderLoaderStartedEvent($shopId, $orderId));

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

        $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_PRE_PERSIST, new OrderEvent($entity, $shopId));

        $this->orderManager->persist($entity);

        $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_POST_PERSIST, new OrderEvent($entity, $shopId));

        $this->orderManager->flush();

        $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_POST_FLUSH, new GenericEvent($entity));
    }
}
