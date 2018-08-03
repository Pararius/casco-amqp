<?php

declare(strict_types=1);

namespace Amqp\CacheWarmer;

use Humus\Amqp\Exchange;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeclareAmqpExchanges extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'amqp:declare:exchanges';

    /**
     * @var Exchange[]
     */
    private $exchanges;

    /**
     * @param Exchange ...$exchanges
     */
    public function __construct(Exchange ...$exchanges)
    {
        $this->exchanges = $exchanges;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->exchanges as $exchange) {
            $output->writeln('Declare exchange ' . $exchange->getName());
            $exchange->declareExchange();
        }

        $output->writeln('Exchanges declared.');
    }
}
