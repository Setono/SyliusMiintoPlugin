<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusMiintoPlugin\OrderFulfiller\OrderFulfillerInterface;
use Setono\SyliusMiintoPlugin\Repository\OrderRepositoryInterface;
use Symfony\Component\Workflow\Registry;

final class PendingOrdersProcessor implements PendingOrdersProcessorInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var OrderFulfillerInterface
     */
    private $orderFulfiller;

    public function __construct(OrderRepositoryInterface $orderRepository, ObjectManager $orderManager, Registry $workflowRegistry, OrderFulfillerInterface $orderFulfiller)
    {
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
        $this->workflowRegistry = $workflowRegistry;
        $this->orderFulfiller = $orderFulfiller;
    }

    public function process(): void
    {
        $orders = $this->orderRepository->findPending();
        foreach ($orders as $order) {
            $workflow = $this->workflowRegistry->get($order);

            if (!$workflow->can($order, 'start_processing')) {
                $workflow->apply($order, 'errored');

                continue;
            }

            $workflow->apply($order, 'start_processing');

            $this->orderFulfiller->fulfill($order);

            if (!$workflow->can($order, 'process')) {
                $workflow->apply($order, 'errored');

                continue;
            }

            $workflow->apply($order, 'process');
        }

        $this->orderManager->flush();
    }
}
