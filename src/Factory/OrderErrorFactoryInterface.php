<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Factory;

use Setono\SyliusMiintoPlugin\Model\OrderErrorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Workflow\TransitionBlocker;

interface OrderErrorFactoryInterface extends FactoryInterface
{
    public function createFromTransitionBlocker(TransitionBlocker $transitionBlocker): OrderErrorInterface;
}
