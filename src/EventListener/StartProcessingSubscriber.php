<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Safe\Exceptions\StringsException;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingTypeMappingRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

final class StartProcessingSubscriber implements EventSubscriberInterface
{
    /**
     * @var PaymentMethodMappingRepositoryInterface
     */
    private $paymentMethodMappingRepository;

    /**
     * @var ShippingMethodMappingRepositoryInterface
     */
    private $shippingMethodMappingRepository;

    /**
     * @var ShippingTypeMappingRepositoryInterface
     */
    private $shippingTypeMappingRepository;

    public function __construct(
        PaymentMethodMappingRepositoryInterface $paymentMethodMappingRepository,
        ShippingMethodMappingRepositoryInterface $shippingMethodMappingRepository,
        ShippingTypeMappingRepositoryInterface $shippingTypeMappingRepository
    ) {
        $this->paymentMethodMappingRepository = $paymentMethodMappingRepository;
        $this->shippingMethodMappingRepository = $shippingMethodMappingRepository;
        $this->shippingTypeMappingRepository = $shippingTypeMappingRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.setono_sylius_miinto_order_processing.guard.start_processing' => [
                'validate',
            ],
        ];
    }

    /**
     * @param GuardEvent $event
     *
     * @return bool
     *
     * @throws StringsException
     */
    public function validate(GuardEvent $event): bool
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return false;
        }

        $shop = $order->getShop();
        if (null === $shop) {
            return $this->block($event, 'No shop set on the order');
        }

        if ($shop->getChannel() === null) {
            return $this->block($event, \Safe\sprintf('The shop %s is not mapped to a channel', (string) $shop->getId()));
        }

        if ($shop->getLocale() === null) {
            return $this->block($event, \Safe\sprintf('The shop %s is not mapped to a locale', (string) $shop->getId()));
        }

        if (!$this->shippingTypeMappingRepository->hasValidMapping($shop, $order->getShippingType())) {
            return $this->block($event, \Safe\sprintf('The shipping type %s on shop %s is not mapped', $order->getShippingType(), (string) $shop->getId()));
        }

        if (!$this->paymentMethodMappingRepository->hasValidMapping($shop)) {
            return $this->block($event, \Safe\sprintf('The shop %s is not mapped to a payment method', (string) $shop->getId()));
        }

        return true;
    }

    private function block(GuardEvent $event, string $message): bool
    {
        $event->addTransitionBlocker(new TransitionBlocker($message, TransitionBlocker::UNKNOWN));

        return false;
    }
}
