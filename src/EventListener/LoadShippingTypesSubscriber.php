<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Event\OrderLoaderStartedEvent;
use Setono\SyliusMiintoPlugin\Model\ShippingType;
use Setono\SyliusMiintoPlugin\Model\ShippingTypeMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingTypeMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LoadShippingTypesSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    private $shopRepository;

    /**
     * @var ShippingTypeMappingRepositoryInterface
     */
    private $shippingTypeMappingRepository;

    /**
     * @var FactoryInterface
     */
    private $shippingTypeMappingFactory;

    public function __construct(
        RepositoryInterface $shopRepository,
        ShippingTypeMappingRepositoryInterface $shippingTypeMappingRepository,
        FactoryInterface $shippingTypeMappingFactory
    ) {
        $this->shopRepository = $shopRepository;
        $this->shippingTypeMappingRepository = $shippingTypeMappingRepository;
        $this->shippingTypeMappingFactory = $shippingTypeMappingFactory;
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

        $shippingTypes = ShippingType::getShippingTypes();
        foreach ($shippingTypes as $shippingType) {
            if ($this->shippingTypeMappingRepository->hasMapping($shop, $shippingType)) {
                continue;
            }

            /** @var ShippingTypeMappingInterface $mapping */
            $mapping = $this->shippingTypeMappingFactory->createNew();
            $mapping->setShop($shop);
            $mapping->setType($shippingType);

            $this->shippingTypeMappingRepository->add($mapping);
        }
    }
}
