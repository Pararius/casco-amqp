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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumer = $this->createCallbackConsumer(
            $output,
            (int) $input->getOption('timeout'),
            function (Envelope $msg) use ($output): DeliveryResult {
                return $this->consume($msg, $output);
            }
        );

        $output->writeln(
            sprintf('Consuming from queue "<comment>%s</comment>"', $this->getQueue()->getName())
        );

        $consumer->consume(
            (int) $input->getOption('limit')
        );

        return 0;
    }

    protected function createCallbackConsumer(OutputInterface $output, int $timeout, callable $delivery): CallbackConsumer
    {
        // Handle signals async (almost immediately).
        pcntl_async_signals(true);

        $consumer = new CallbackConsumer(
            $this->getQueue(),
            new ConsoleLogger($output),
            $timeout,
            $delivery
        );

        $shutdownWrapper = function () use ($consumer) {
            $this->shutdown();
            $consumer->shutdown();
        };

        pcntl_signal(SIGTERM, $shutdownWrapper);
        pcntl_signal(SIGINT, $shutdownWrapper);
        pcntl_signal(SIGHUP, $shutdownWrapper);

        return $consumer;
    }

    abstract protected function consume(Envelope $envelope, OutputInterface $output): DeliveryResult;

    abstract protected function getQueue(): Queue;

    public function shutdown(): void
    {
        // noop
    }
}
