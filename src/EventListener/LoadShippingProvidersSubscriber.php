<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Event\OrderLoaderStartedEvent;
use Setono\SyliusMiintoPlugin\Model\ShippingMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LoadShippingProvidersSubscriber implements EventSubscriberInterface
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
     * @var ShippingMethodMappingRepositoryInterface
     */
    private $shippingMethodMappingRepository;

    /**
     * @var FactoryInterface
     */
    private $shippingMethodMappingFactory;

    public function __construct(
        ClientInterface $client,
        RepositoryInterface $shopRepository,
        ShippingMethodMappingRepositoryInterface $shippingMethodMappingRepository,
        FactoryInterface $shippingMethodMappingFactory
    ) {
        $this->client = $client;
        $this->shopRepository = $shopRepository;
        $this->shippingMethodMappingRepository = $shippingMethodMappingRepository;
        $this->shippingMethodMappingFactory = $shippingMethodMappingFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetonoSyliusMiintoEvents::ORDER_LOADER_STARTED => [
                'load',
            ],
        ];
    }

    public function load(OrderLoaderStartedEvent $event): void
    {
        /** @var ShopInterface|null $shop */
        $shop = $this->shopRepository->find($event->getShopId());
        if (null === $shop) {
            return;
        }

        $shippingProviders = $this->client->getShippingProviders($event->getShopId(), $event->getOrderId());

        foreach ($shippingProviders as $shippingProvider) {
            if ($this->shippingMethodMappingRepository->hasMapping($shop, $shippingProvider['id'])) {
                continue;
            }

            /** @var ShippingMethodMappingInterface $mapping */
            $mapping = $this->shippingMethodMappingFactory->createNew();
            $mapping->setShop($shop);
            $mapping->setProviderId($shippingProvider['id']);

            $this->shippingMethodMappingRepository->add($mapping);
        }
    }
}
