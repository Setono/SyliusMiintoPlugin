<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\EventListener;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

final class ProcessSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.setono_sylius_miinto_order_processing.guard.process' => [
                'validate',
            ],
        ];
    }

    public function validate(GuardEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return;
        }

        if ($order->getOrder() === null) {
            $event->addTransitionBlocker(new TransitionBlocker('No shop order associated with this Miinto order', TransitionBlocker::UNKNOWN));
        }
    }
}
