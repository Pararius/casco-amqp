<?php

declare(strict_types=1);

namespace Amqp\Console;

use Humus\Amqp\CallbackConsumer;
use Humus\Amqp\DeliveryResult;
use Humus\Amqp\Envelope;
use Humus\Amqp\Queue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ConsumeCommand extends Command
{
    /** @inheritdoc */
    protected function configure(): void
    {
        $this->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Idle timeout in seconds', 0);
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of messages to consume', 0);
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $delivery = function (Envelope $msg) use ($output): DeliveryResult {
            return $this->consume($msg, $output);
        };

        $consumer = $this->createCallbackConsumer($input, $output, $delivery);

        $consumer->consume((int) $input->getOption('limit'));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param                 $delivery
     *
     * @return CallbackConsumer
     */
    protected function createCallbackConsumer(InputInterface $input, OutputInterface $output, $delivery): CallbackConsumer
    {
        // Handle signals async (almost immediately).
        pcntl_async_signals(true);

        $consumer = new CallbackConsumer(
            $this->getQueue(),
            new ConsoleLogger($output),
            (int) $input->getOption('timeout'),
            $delivery
        );

        return $consumer;
    }

    /**
     * @param Envelope        $envelope
     * @param OutputInterface $output
     *
     * @return DeliveryResult
     */
    abstract protected function consume(Envelope $envelope, OutputInterface $output): DeliveryResult;

    /**
     * @return Queue
     */
    abstract protected function getQueue(): Queue;
}

