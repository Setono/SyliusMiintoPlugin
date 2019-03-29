<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderErrorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Workflow\TransitionBlocker;

final class OrderErrorFactory implements OrderErrorFactoryInterface
{
    private $decoratedFactory;

    public function __construct(FactoryInterface $factory)
    {
        $this->decoratedFactory = $factory;
    }

    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    public function createFromTransitionBlocker(TransitionBlocker $transitionBlocker): OrderErrorInterface
    {
        /** @var OrderErrorInterface $orderError */
        $orderError = $this->decoratedFactory->createNew();

        $orderError->setMessage($transitionBlocker->getMessage());
        // todo add context field to OrderError so we can add misc parameters

        return $orderError;
    }
}
