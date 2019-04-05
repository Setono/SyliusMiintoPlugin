<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderErrorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Workflow\TransitionBlocker;

final class OrderErrorFactory implements OrderErrorFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    public function __construct(FactoryInterface $factory)
    {
        $this->decoratedFactory = $factory;
    }

    public function createNew(): OrderErrorInterface
    {
        /** @var OrderErrorInterface $error */
        $error = $this->decoratedFactory->createNew();

        return $error;
    }

    public function createFromThrowable(\Throwable $e): OrderErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($e->getMessage());

        return $error;
    }

    public function createFromTransitionBlocker(TransitionBlocker $transitionBlocker): OrderErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($transitionBlocker->getMessage());

        // todo add context field to OrderError so we can add misc parameters

        return $error;
    }

    public function createFromConstraintViolation(ConstraintViolationInterface $constraintViolation): OrderErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($constraintViolation->getMessage());

        return $error;
    }
}
