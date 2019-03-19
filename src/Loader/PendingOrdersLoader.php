<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PendingOrdersLoader implements PendingOrdersLoaderInterface
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

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClientInterface $client,
        RepositoryInterface $orderRepository,
        FactoryInterface $orderFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->client = $client;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
    }

    public function load(): void
    {
        $shopIds = $this->client->getShopIds();

        foreach ($shopIds as $shopId) {
            $orders = $this->client->getOrders($shopId, [
                'status' => ['pending']
            ]);

            foreach ($orders['data'] as $order) {
                /** @var OrderInterface $entity */
                $entity = $this->orderRepository->find($order['id']); // @todo if the entity is found we should handle this, since this is not intended
                if(null === $entity) {
                    $entity = $this->orderFactory->createNew();
                    $entity->setId($order['id']);
                }

                $entity->setData($order);

                $this->orderRepository->add($entity); // @todo should we inject the manager and flush less often?

                $this->eventDispatcher->dispatch('setono_sylius_miinto.loader.order.post_flush', new GenericEvent($entity));
            }
        }
    }
}
