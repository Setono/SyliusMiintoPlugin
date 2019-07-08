<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Processor\PendingTransfersProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class PendingTransfersCommand extends Command
{
    protected static $defaultName = 'setono:sylius-miinto:pending-transfers';

    /** @var PendingTransfersProcessorInterface */
    private $pendingTransfersProcessor;

    public function __construct(PendingTransfersProcessorInterface $pendingTransfersProcessor)
    {
        parent::__construct();

        $this->pendingTransfersProcessor = $pendingTransfersProcessor;
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch and process pending transfers');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->pendingTransfersProcessor->setLogger(new ConsoleLogger($output));
        $this->pendingTransfersProcessor->process();

        return 0;
    }
}
