<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ShipmentProvider implements ShipmentProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $shipmentFactory;

    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    public function __construct(FactoryInterface $shipmentFactory, MappingRepositoryInterface $mappingRepository)
    {
        $this->shipmentFactory = $shipmentFactory;
        $this->mappingRepository = $mappingRepository;
    }

    public function provide(OrderInterface $order): ShipmentInterface
    {
        $shop = $order->getShop();
        if (null === $shop) {
            throw new \RuntimeException('No shop set on order'); // @todo better exception
        }

        $providerId = $order->getProviderId();
        if (null === $providerId) {
            throw new \RuntimeException('No provider id set on order'); // @todo better exception
        }

        $mapping = $this->mappingRepository->findMappedByShopAndProviderId($shop, $providerId);

        if (null === $mapping) {
            throw new \RuntimeException('No mapping for given shop and provider id'); // @todo better exception
        }

        $shippingMethod = $mapping->getShippingMethod();
        if (null === $shippingMethod) {
            throw new \RuntimeException('No shipping method set on the given mapping'); // @todo better exception
        }

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();

        $shipment->setMethod($shippingMethod);

        return $shipment;
    }
}
