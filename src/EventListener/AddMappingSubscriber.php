<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AddMappingSubscriber implements EventSubscriberInterface
{
    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    /**
     * @var FactoryInterface
     */
    private $mappingFactory;

    /**
     * @var array
     */
    private $shopCache = [];

    public function __construct(MappingRepositoryInterface $mappingRepository, FactoryInterface $mappingFactory)
    {
        $this->mappingRepository = $mappingRepository;
        $this->mappingFactory = $mappingFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetonoSyliusMiintoEvents::ORDER_LOADER_POST_FLUSH => [
                'add'
            ]
        ];
    }

    public function add(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if(!$order instanceof OrderInterface) {
            return;
        }

        if($order->getShop() === null || $order->getProviderId() === null) {
            return;
        }

        $mapping = $this->mappingRepository->findOneByShopAndProviderId($order->getShop(), $order->getProviderId());

        if($mapping !== null) {
            return;
        }

        /** @var MappingInterface $mapping */
        $mapping = $this->mappingFactory->createNew();
        $mapping->setShop($order->getShop());
        $mapping->setProviderId($order->getProviderId());

        $this->mappingRepository->add($mapping);
    }
}
