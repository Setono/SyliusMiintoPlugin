<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\PaymentMethodMappingRepositoryInterface;
use Setono\SyliusMiintoPlugin\Repository\ShippingTypeMappingRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

final class StartProcessingSubscriber implements EventSubscriberInterface
{
    /** @var PaymentMethodMappingRepositoryInterface */
    private $paymentMethodMappingRepository;

    /** @var ShippingTypeMappingRepositoryInterface */
    private $shippingTypeMappingRepository;

    public function __construct(
        PaymentMethodMappingRepositoryInterface $paymentMethodMappingRepository,
        ShippingTypeMappingRepositoryInterface $shippingTypeMappingRepository
    ) {
        $this->paymentMethodMappingRepository = $paymentMethodMappingRepository;
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
     * @throws StringsException
     */
    public function validate(GuardEvent $event): bool
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return false;
        }

        $shop = $order->getShop();
        if (null === $shop) {
            return $this->block($event, 'No shop set on the order');
        }

        if ($shop->getChannel() === null) {
            return $this->block($event, sprintf(
                'The shop %s is not mapped to a channel. ' .
                'You should go to Admin > Miinto > Shops and choose channel for this shop.',
                (string) $shop->getId()
            ));
        }

        if ($shop->getLocale() === null) {
            return $this->block($event, sprintf(
                'The shop %s is not mapped to a locale. ' .
                'You should go to Admin > Miinto > Shops and choose locale for this shop.',
                (string) $shop->getId()
            ));
        }

        if (!$this->shippingTypeMappingRepository->hasValidMapping($shop, $order->getShippingType())) {
            return $this->block($event, sprintf(
                'The shipping type %s on shop %s is not mapped. ' .
                'You should go to Admin > Miinto > Shipping type mappings and choose shipping method for it.',
                $order->getShippingType(),
                (string) $shop->getId()
            ));
        }

        if (!$this->paymentMethodMappingRepository->hasValidMapping($shop)) {
            return $this->block($event, sprintf(
                'The shop %s is not mapped to a payment method. ' .
                'You should go to Admin > Miinto > Payment method mappings and choose payment method for it.',
                (string) $shop->getId()
            ));
        }

        return true;
    }

    private function block(GuardEvent $event, string $message): bool
    {
        $event->addTransitionBlocker(new TransitionBlocker($message, TransitionBlocker::UNKNOWN));

        return false;
    }
}
