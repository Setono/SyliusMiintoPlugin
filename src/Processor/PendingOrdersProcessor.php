<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusMiintoPlugin\Factory\OrderErrorFactoryInterface;
use Setono\SyliusMiintoPlugin\OrderFulfiller\OrderFulfillerInterface;
use Setono\SyliusMiintoPlugin\Repository\OrderRepositoryInterface;
use Symfony\Component\Workflow\Exception\TransitionException;
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

    /**
     * @var OrderErrorFactoryInterface
     */
    private $orderErrorFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        Registry $workflowRegistry,
        OrderFulfillerInterface $orderFulfiller,
        OrderErrorFactoryInterface $orderErrorFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
        $this->workflowRegistry = $workflowRegistry;
        $this->orderFulfiller = $orderFulfiller;
        $this->orderErrorFactory = $orderErrorFactory;
    }

    public function process(): void
    {
        $orders = $this->orderRepository->findPending();
        foreach ($orders as $order) {
            $workflow = $this->workflowRegistry->get($order);

            try {
                $workflow->apply($order, 'start_processing');

                $orderFulfillment = $this->orderFulfiller->fulfill($order);

                $workflow->apply($order, 'process');
            } catch (TransitionException $e) {
                if($workflow->can($order, 'errored')) {
                    $workflow->apply($order, 'errored');
                }

                $transitionBlockerList = $workflow->buildTransitionBlockerList($order, $e->getTransitionName());

                foreach ($transitionBlockerList as $transitionBlocker) {
                    $orderError = $this->orderErrorFactory->createFromTransitionBlocker($transitionBlocker);

                    $order->addError($orderError);
                }
            }
        }

        $this->orderManager->flush();
    }
}
