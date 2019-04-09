<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\ErrorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Workflow\TransitionBlocker;

interface ErrorFactoryInterface extends FactoryInterface
{
    public function createFromThrowable(\Throwable $e): ErrorInterface;

    public function createFromTransitionBlocker(TransitionBlocker $transitionBlocker): ErrorInterface;

    public function createFromConstraintViolation(ConstraintViolationInterface $constraintViolation): ErrorInterface;
}
