<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Handler\PendingTransfersHandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class PendingTransfersCommand extends Command
{
    /**
     * @var PendingTransfersHandlerInterface
     */
    private $handler;

    public function __construct(PendingTransfersHandlerInterface $handler)
    {
        parent::__construct();

        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this->setName('setono:sylius-miinto:pending-transfers');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->handler->setLogger(new ConsoleLogger($output));
        $this->handler->handle();
    }
}
