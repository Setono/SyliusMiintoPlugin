<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Handler;

use Psr\Log\LoggerAwareTrait;
use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Setono\SyliusMiintoPlugin\Resolver\PositionResolverInterface;

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

    public function __construct(ClientInterface $client, PositionResolverInterface $positionResolver)
    {
        $this->client = $client;
        $this->positionResolver = $positionResolver;
    }

    public function handle(): void
    {
        $this->logger->info('Handling pending transfers...');

        $shopIds = $this->client->getShopIds();

        foreach ($shopIds as $shopId) {
            $transfers = $this->client->getTransfers($shopId);

            foreach ($transfers as $transfer) {
                $positions = $this->positionResolver->resolve($transfer['pendingPositions']);

                $this->client->updateTransfer($shopId, $transfer['id'], $positions);
            }
        }
    }
}
