<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderEvent;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
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

                // @todo await answer from Miinto regarding the overriding of provider id
                $providerId = $order['shippingInformation']['deliveryOptions']['override']['providerId'] ?? '';
                if('' === $providerId) {
                    $providerId = $order['shippingInformation']['deliveryOptions']['initial']['providerId'];
                }

                $entity->setData($order);
                $entity->setProviderId($providerId);

                $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_PRE_PERSIST, new OrderEvent($entity, $shopId));

                $this->orderManager->persist($entity);

                $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_POST_PERSIST, new OrderEvent($entity, $shopId));

                $this->orderManager->flush();

                $this->eventDispatcher->dispatch(SetonoSyliusMiintoEvents::ORDER_LOADER_POST_FLUSH, new GenericEvent($entity));
            }
        }
    }
}
