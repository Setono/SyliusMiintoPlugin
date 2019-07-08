<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderPrePersistEvent;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddShopSubscriber implements EventSubscriberInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var RepositoryInterface */
    private $shopRepository;

    /** @var FactoryInterface */
    private $shopFactory;

    /** @var ShopInterface[] */
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
            OrderLoaderPrePersistEvent::class => 'add',
        ];
    }

    public function add(OrderLoaderPrePersistEvent $event): void
    {
        if (isset($this->shopCache[$event->getShopId()])) {
            $shop = $this->shopCache[$event->getShopId()];
        } else {
            $shopDetails = $this->client->getShopDetails($event->getShopId());

            /** @var ShopInterface|null $shop */
            $shop = $this->shopRepository->find($event->getShopId());
            if (null === $shop) {
                /** @var ShopInterface $shop */
                $shop = $this->shopFactory->createNew();
                $shop->setId($event->getShopId());
            }

            $shop->setName($shopDetails['name'] ?? 'Not available');

            $this->shopCache[$event->getShopId()] = $shop;
        }

        $event->getOrder()->setShop($shop);
    }
}
