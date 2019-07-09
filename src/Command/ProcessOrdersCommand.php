<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Command;

use Setono\SyliusMiintoPlugin\Processor\PendingOrdersProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class ProcessOrdersCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-miinto:process-orders';

    /** @var PendingOrdersProcessorInterface */
    private $processor;

    public function __construct(PendingOrdersProcessorInterface $processor)
    {
        parent::__construct();

        $this->processor = $processor;
    }

    protected function configure(): void
    {
        $this->setDescription('Process orders')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of orders to process', 0)
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            return 0;
        }

        $limit = $input->getOption('limit');
        if (!is_numeric($limit)) {
            $limit = 0;
        }

        $this->processor->setLogger(new ConsoleLogger($output));
        $this->processor->process((int) $limit);

        $this->release();

        return 0;
    }
}
