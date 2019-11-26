<?php

declare(strict_types=1);

namespace Amqp;

use Humus\Amqp\Channel;
use Humus\Amqp\Connection;
use Humus\Amqp\ConnectionOptions;
use Humus\Amqp\Constants;
use Humus\Amqp\Driver\PhpAmqpLib\LazyConnection as AmqpConnection;
use Humus\Amqp\Exchange;
use Humus\Amqp\Queue;

class HumusAmqpFactory
{
    public static function createConnection(ConnectionOptions $options): Connection
    {
        return new AmqpConnection($options);
    }

    public static function createChannel(Connection $connection, int $prefetchCount = 20): Channel
    {
        $channel = $connection->newChannel();
        $channel->setPrefetchCount($prefetchCount);

        return $channel;
    }

    public static function createExchange(
        Channel $channel,
        string $name,
        string $type = 'topic',
        int $flags = Constants::AMQP_DURABLE,
        array $args = []
    ): Exchange {
        $exchange = $channel->newExchange();
        $exchange->setName($name);
        $exchange->setType($type);
        $exchange->setFlags($flags);
        $exchange->setArguments($args);
        $exchange->declareExchange();

        return $exchange;
    }

    public static function createDelayedExchange(
        Channel $channel,
        string $name,
        string $type = 'topic',
        int $flags = Constants::AMQP_DURABLE
    ): Exchange {
        return static::createExchange(
            $channel,
            $name,
            'x-delayed-message',
            $flags,
            [
                'x-delayed-type' => $type,
            ]
        );
    }

    public static function createQueue(
        Channel $channel,
        string $name,
        int $flags = Constants::AMQP_DURABLE
    ): Queue {
        $queue = $channel->newQueue();
        $queue->setName($name);
        $queue->setFlags($flags);
        $queue->declareQueue();

        return $queue;
    }
}
