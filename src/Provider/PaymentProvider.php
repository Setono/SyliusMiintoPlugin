<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PaymentProvider implements PaymentProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $paymentFactory;

    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    public function __construct(FactoryInterface $paymentFactory, MappingRepositoryInterface $mappingRepository)
    {
        $this->paymentFactory = $paymentFactory;
        $this->mappingRepository = $mappingRepository;
    }

    public function provide(OrderInterface $order): PaymentInterface
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

        $paymentMethod = $mapping->getPaymentMethod();
        if (null === $paymentMethod) {
            throw new \RuntimeException('No payment method set on the given mapping'); // @todo better exception
        }

        /** @var PaymentInterface $payment */
        $payment = $this->paymentFactory->createNew();

        $data = $order->getData();
        $payment->setCurrencyCode($data['currency']);
        $payment->setMethod($paymentMethod);

        return $payment;
    }
}
