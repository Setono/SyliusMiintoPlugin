<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Loader\PendingOrdersLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PendingOrdersCommand extends Command
{
    /**
     * @var PendingOrdersLoaderInterface
     */
    private $loader;

    public function __construct(PendingOrdersLoaderInterface $loader)
    {
        parent::__construct();

        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this->setName('setono:sylius-miinto:pending-orders');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->loader->load();
    }
}
