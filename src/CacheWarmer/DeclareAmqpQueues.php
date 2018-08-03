<?php

declare(strict_types=1);

namespace Amqp\CacheWarmer;

use Humus\Amqp\Queue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeclareAmqpQueues extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'amqp:declare:queues';

    /**
     * @var Queue[]
     */
    private $queues;

    /**
     * @param Queue ...$queues
     */
    public function __construct(Queue ...$queues)
    {
        $this->queues = $queues;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->queues as $queue) {
            $output->writeln('Create queue ' . $queue->getName());
            $queue->declareQueue();
        }

        $output->writeln('Queues finished.');
    }
}
