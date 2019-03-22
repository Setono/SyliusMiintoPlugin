<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderEvent;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddShopSubscriber implements EventSubscriberInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RepositoryInterface
     */
    private $shopRepository;

    /**
     * @var FactoryInterface
     */
    private $shopFactory;

    /**
     * @var array
     */
    private $shopCache = [];

    public function __construct(ClientInterface $client, RepositoryInterface $shopRepository, FactoryInterface $shopFactory)
    {
        $this->client = $client;
        $this->shopRepository = $shopRepository;
        $this->shopFactory = $shopFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetonoSyliusMiintoEvents::ORDER_LOADER_PRE_PERSIST => [
                'add'
            ]
        ];
    }

    public function add(OrderEvent $event): void
    {
        if(isset($this->shopCache[$event->getShopId()])) {
            $shop = $this->shopCache[$event->getShopId()];
        } else {
            $data = $this->client->getShopDetails($event->getShopId());

            /** @var ShopInterface $shop */
            $shop = $this->shopRepository->find($event->getShopId());
            if(null === $shop) {
                $shop = $this->shopFactory->createNew();
                $shop->setId($event->getShopId());
            }

            $shop->setName($data['data']['name'] ?? 'Not available');

            $this->shopCache[$event->getShopId()] = $shop;
        }

        $event->getOrder()->setShop($shop);
    }
}
