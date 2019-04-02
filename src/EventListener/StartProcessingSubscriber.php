<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Safe\Exceptions\StringsException;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

final class StartProcessingSubscriber implements EventSubscriberInterface
{
    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    public function __construct(MappingRepositoryInterface $mappingRepository)
    {
        $this->mappingRepository = $mappingRepository;
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
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return false;
        }

        $shop = $order->getShop();
        if (null === $shop) {
            return $this->block($event, 'No shop set on the order');
        }

        $providerId = $order->getProviderId();
        if (null === $providerId) {
            return $this->block($event, 'No provider id set on the order');
        }

        if ($shop->getChannel() === null) {
            return $this->block($event, \Safe\sprintf('The shop %s is not mapped to a channel', (string) $shop->getId()));
        }

        if (!$this->mappingRepository->hasMapping($shop, $providerId)) {
            return $this->block($event, \Safe\sprintf('No mapping between the shop %s and the provider id %s', $shop->getId(), $providerId));
        }

        return true;
    }

    private function block(GuardEvent $event, string $message): bool
    {
        $event->addTransitionBlocker(new TransitionBlocker($message, TransitionBlocker::UNKNOWN));

        return false;
    }
}
