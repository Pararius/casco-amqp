<?php

declare(strict_types=1);

namespace Amqp;

use Humus\Amqp\Channel;
use Humus\Amqp\Connection;
use Humus\Amqp\ConnectionOptions;
use Humus\Amqp\Constants;
use Humus\Amqp\Driver\AmqpExtension\Connection as AmqpExtensionConnection;
use Humus\Amqp\Exchange;
use Humus\Amqp\Queue;

class HumusAmqpFactory
{
    /**
     * @param ConnectionOptions $options
     *
     * @return Connection
     */
    public static function createConnection(ConnectionOptions $options): Connection
    {
        $connection = new AmqpExtensionConnection($options);
        $connection->connect();

        return $connection;
    }

    /**
     * @param Connection $connection
     * @param int        $prefetchCount
     *
     * @return Channel
     */
    public static function createChannel(Connection $connection, $prefetchCount = 20): Channel
    {
        $channel = $connection->newChannel();
        $channel->setPrefetchCount($prefetchCount);

        return $channel;
    }

    /**
     * @param Channel $channel
     * @param string  $name
     * @param string  $type
     * @param int     $flags
     *
     * @return Exchange
     */
    public static function createExchange(
        Channel $channel,
        string $name,
        string $type = 'topic',
        int $flags = Constants::AMQP_DURABLE
    ): Exchange {
        $exchange = $channel->newExchange();
        $exchange->setName($name);
        $exchange->setType($type);
        $exchange->setFlags($flags);
        $exchange->declareExchange();

        return $exchange;
    }

    /**
     * @param Channel $channel
     * @param string  $name
     * @param int     $flags
     *
     * @return Queue
     */
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
