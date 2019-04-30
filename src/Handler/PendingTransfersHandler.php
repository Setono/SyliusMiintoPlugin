<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Handler;

use Psr\Log\LoggerAwareTrait;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Message\Command\LoadOrder;
use Setono\SyliusMiintoPlugin\Resolver\PositionResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PendingTransfersHandler implements PendingTransfersHandlerInterface
{
    use LoggerAwareTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PositionResolverInterface
     */
    private $positionResolver;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(ClientInterface $client, PositionResolverInterface $positionResolver, MessageBusInterface $messageBus)
    {
        $this->client = $client;
        $this->positionResolver = $positionResolver;
        $this->messageBus = $messageBus;
    }

    public function handle(): void
    {
        $this->logger->info('Handling pending transfers...');

        $shopIds = $this->client->getShopIds();

        foreach ($shopIds as $shopId) {
            $transfers = $this->client->getTransfers($shopId);

            foreach ($transfers as $transfer) {
                $positions = $this->positionResolver->resolve($transfer['pendingPositions']);

                $orderId = $this->client->updateTransfer($shopId, $transfer['id'], $positions);

                if (null !== $orderId) {
                    $this->messageBus->dispatch(new LoadOrder($shopId, $orderId));
                }
            }
        }
    }
}
