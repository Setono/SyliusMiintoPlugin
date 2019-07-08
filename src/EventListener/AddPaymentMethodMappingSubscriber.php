<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Event\OrderLoaderPostFlushEvent;
use Setono\SyliusMiintoPlugin\Model\PaymentMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddPaymentMethodMappingSubscriber implements EventSubscriberInterface
{
    /** @var PaymentMethodMappingRepositoryInterface */
    private $paymentMethodMappingRepository;

    /** @var FactoryInterface */
    private $paymentMethodMappingFactory;

    public function __construct(PaymentMethodMappingRepositoryInterface $paymentMethodMappingRepository, FactoryInterface $paymentMethodMappingFactory)
    {
        $this->paymentMethodMappingRepository = $paymentMethodMappingRepository;
        $this->paymentMethodMappingFactory = $paymentMethodMappingFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderLoaderPostFlushEvent::class => 'add',
        ];
    }

    public function add(OrderLoaderPostFlushEvent $event): void
    {
        $order = $event->getOrder();

        if ($order->getShop() === null) {
            return;
        }

        if ($this->paymentMethodMappingRepository->hasMapping($order->getShop())) {
            return;
        }

        /** @var PaymentMethodMappingInterface $mapping */
        $mapping = $this->paymentMethodMappingFactory->createNew();
        $mapping->setShop($order->getShop());

        $this->paymentMethodMappingRepository->add($mapping);
    }
}
