<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PrefetchProductMapCommand extends Command
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    protected function configure(): void
    {
        $this->setName('setono:sylius-miinto:prefetch-product-map');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->client->getShopIds() as $shopId) {
            $output->writeln($shopId);
            $productMap = $this->client->getProductMap($shopId);
            dump($productMap);
        }
    }
}
