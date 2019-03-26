<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Processor\PendingOrdersProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ProcessOrdersCommand extends Command
{
    /**
     * @var PendingOrdersProcessorInterface
     */
    private $processor;

    public function __construct(PendingOrdersProcessorInterface $processor)
    {
        parent::__construct();

        $this->processor = $processor;
    }

    protected function configure(): void
    {
        $this->setName('setono:sylius-miinto:process-orders');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->processor->process();
    }
}
