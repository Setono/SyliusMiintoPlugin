<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Model\PaymentMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\SetonoSyliusMiintoEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AddPaymentMethodMappingSubscriber implements EventSubscriberInterface
{
    /**
     * @var PaymentMethodMappingRepositoryInterface
     */
    private $paymentMethodMappingRepository;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodMappingFactory;

    public function __construct(PaymentMethodMappingRepositoryInterface $paymentMethodMappingRepository, FactoryInterface $paymentMethodMappingFactory)
    {
        $this->paymentMethodMappingRepository = $paymentMethodMappingRepository;
        $this->paymentMethodMappingFactory = $paymentMethodMappingFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetonoSyliusMiintoEvents::ORDER_LOADER_POST_FLUSH => [
                'add',
            ],
        ];
    }

    public function add(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

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
