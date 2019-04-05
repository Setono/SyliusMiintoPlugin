<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use Setono\SyliusMiintoPlugin\Exception\ConstraintViolationException;
use Setono\SyliusMiintoPlugin\Factory\OrderErrorFactoryInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\OrderFulfiller\OrderFulfillerInterface;
use Setono\SyliusMiintoPlugin\OrderUpdater\OrderUpdaterInterface;
use Setono\SyliusMiintoPlugin\Repository\OrderRepositoryInterface;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

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
     * @var OrderUpdaterInterface
     */
    private $orderUpdater;

    /**
     * @var OrderErrorFactoryInterface
     */
    private $orderErrorFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        Registry $workflowRegistry,
        OrderFulfillerInterface $orderFulfiller,
        OrderUpdaterInterface $orderUpdater,
        OrderErrorFactoryInterface $orderErrorFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
        $this->workflowRegistry = $workflowRegistry;
        $this->orderFulfiller = $orderFulfiller;
        $this->orderUpdater = $orderUpdater;
        $this->orderErrorFactory = $orderErrorFactory;
    }

    public function process(): void
    {
        $orders = $this->orderRepository->findPending();
        foreach ($orders as $order) {
            $workflow = $this->workflowRegistry->get($order);

            try {
                $order->clearErrors();

                $workflow->apply($order, 'start_processing');

                $orderFulfillment = $this->orderFulfiller->fulfill($order);
                $this->orderUpdater->update($orderFulfillment);

                $workflow->apply($order, 'process');
            } catch (TransitionException $e) {
                $transitionBlockerList = $workflow->buildTransitionBlockerList($order, $e->getTransitionName());

                foreach ($transitionBlockerList as $transitionBlocker) {
                    $error = $this->orderErrorFactory->createFromTransitionBlocker($transitionBlocker);

                    $order->addError($error);
                }

                $this->errored($workflow, $order);
            } catch (ConstraintViolationException $e) {
                foreach ($e->getConstraintViolationList() as $violation) {
                    $error = $this->orderErrorFactory->createFromConstraintViolation($violation);

                    $order->addError($error);
                }

                $this->errored($workflow, $order);
            } catch (InvalidArgumentException $e) {
                $error = $this->orderErrorFactory->createFromThrowable($e);

                $order->addError($error);

                $this->errored($workflow, $order);
            }

            $this->orderManager->flush();
        }
    }

    private function errored(Workflow $workflow, OrderInterface $order): void
    {
        if ($workflow->can($order, 'errored')) {
            $workflow->apply($order, 'errored');
        }
    }
}
