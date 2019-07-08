<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\ErrorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Workflow\TransitionBlocker;
use Throwable;

final class ErrorFactory implements ErrorFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $factory)
    {
        $this->decoratedFactory = $factory;
    }

    public function createNew(): ErrorInterface
    {
        /** @var ErrorInterface $error */
        $error = $this->decoratedFactory->createNew();

        return $error;
    }

    public function createFromThrowable(Throwable $e): ErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($e->getMessage());

        return $error;
    }

    public function createFromTransitionBlocker(TransitionBlocker $transitionBlocker): ErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($transitionBlocker->getMessage());

        // todo add context field to Error so we can add misc parameters

        return $error;
    }

    public function createFromConstraintViolation(ConstraintViolationInterface $constraintViolation): ErrorInterface
    {
        $error = $this->createNew();
        $error->setMessage($constraintViolation->getMessage());

        return $error;
    }
}
