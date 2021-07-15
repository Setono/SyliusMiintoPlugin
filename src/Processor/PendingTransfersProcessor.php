<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Processor;

use Psr\Log\LoggerAwareTrait;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Message\Command\LoadOrder;
use Setono\SyliusMiintoPlugin\Resolver\PositionResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PendingTransfersProcessor implements PendingTransfersProcessorInterface
{
    use LoggerAwareTrait;

    /** @var ClientInterface */
    private $client;

    /** @var PositionResolverInterface */
    private $positionResolver;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(ClientInterface $client, PositionResolverInterface $positionResolver, MessageBusInterface $messageBus)
    {
        $this->client = $client;
        $this->positionResolver = $positionResolver;
        $this->messageBus = $messageBus;
    }

    public function process(): void
    {
        $this->logger->info('Processing pending transfers...');

        $shopIds = $this->client->getShopIds();
        $this->logger->info(sprintf(
            'Shops to process: %s',
            implode(', ', $shopIds)
        ));

        foreach ($shopIds as $shopId) {
            $this->logger->info(sprintf('Processing pending transfers for shop %s...', $shopId));

            $transfers = $this->client->getTransfers($shopId);

            foreach ($transfers as $transfer) {
                $this->logger->info(sprintf('Processing transfer %s...', $transfer['id']));
                $positions = $this->positionResolver->resolve($transfer['pendingPositions']);

                $orderId = $this->client->updateTransfer($shopId, $transfer['id'], $positions);

                if (null !== $orderId) {
                    $this->messageBus->dispatch(new LoadOrder($shopId, $orderId));
                }
            }
        }
    }
}
