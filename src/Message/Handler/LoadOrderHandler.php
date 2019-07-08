<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Message\Handler;

use Setono\SyliusMiintoPlugin\Loader\OrderLoaderInterface;
use Setono\SyliusMiintoPlugin\Message\Command\LoadOrder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class LoadOrderHandler implements MessageHandlerInterface
{
    /** @var OrderLoaderInterface */
    private $orderLoader;

    public function __construct(OrderLoaderInterface $orderLoader)
    {
        $this->orderLoader = $orderLoader;
    }

    public function __invoke(LoadOrder $message): void
    {
        $this->orderLoader->load($message->getShopId(), $message->getOrderId());
    }
}
