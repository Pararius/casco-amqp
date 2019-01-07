<?php

declare(strict_types=1);

namespace Tests;

use Amqp\HumusAmqpFactory;
use Humus\Amqp\Channel;
use Humus\Amqp\Connection;
use Humus\Amqp\ConnectionOptions;
use Humus\Amqp\Constants;
use Humus\Amqp\Exchange;
use PHPUnit\Framework\TestCase;

class HumusAmqpFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_a_connection(): Connection
    {
        $options = new ConnectionOptions();
        $options->setHost(getenv('AMQP_HOST'));
        $options->setLogin(getenv('AMQP_USER'));
        $options->setPassword(getenv('AMQP_PASS'));

        $connection = HumusAmqpFactory::createConnection($options);
        $connection->newChannel();

        $this->assertTrue(
            $connection->isConnected()
        );

        return $connection;
    }

    /**
     * @test
     * @depends it_can_create_a_connection
     *
     * @param Connection $connection
     *
     * @return Channel
     */
    public function it_can_create_a_channel(Connection $connection): Channel
    {
        $prefetchCount = 10;
        $channel = HumusAmqpFactory::createChannel($connection, $prefetchCount);

        $this->assertSame(
            $prefetchCount,
            $channel->getPrefetchCount()
        );

        return $channel;
    }

    /**
     * @test
     * @depends it_can_create_a_channel
     *
     * @param Channel $channel
     *
     * @return Exchange
     */
    public function it_can_create_an_exchange(Channel $channel): Exchange
    {
        $name = $this->generateName();
        $type = 'fanout';
        $flags = Constants::AMQP_AUTODELETE;

        $exchange = HumusAmqpFactory::createExchange(
            $channel,
            $name,
            $type,
            $flags
        );

        $this->assertSame($name, $exchange->getName());
        $this->assertSame($type, $exchange->getType());
        $this->assertSame($flags, $exchange->getFlags());

        return $exchange;
    }

    /**
     * @test
     * @depends it_can_create_a_channel
     *
     * @param Channel $channel
     */
    public function it_creates_an_exchange_with_defaults(Channel $channel): void
    {
        $exchange = HumusAmqpFactory::createExchange(
            $channel,
            $this->generateName()
        );

        $this->assertSame('topic', $exchange->getType());
        $this->assertSame(Constants::AMQP_DURABLE, $exchange->getFlags());
    }

    /**
     * @test
     * @depends it_can_create_a_channel
     *
     * @param Channel $channel
     */
    public function it_can_create_a_queue(Channel $channel): void
    {
        $name = $this->generateName();
        $flags = Constants::AMQP_EXCLUSIVE;

        $queue = HumusAmqpFactory::createQueue(
            $channel,
            $name,
            $flags
        );

        $this->assertSame($name, $queue->getName());
        $this->assertSame($flags, $queue->getFlags());
    }

    /**
     * @test
     * @depends it_can_create_a_channel
     *
     * @param Channel $channel
     */
    public function it_creates_a_queue_with_defaults(Channel $channel): void
    {
        $queue = HumusAmqpFactory::createQueue(
            $channel,
            $this->generateName()
        );

        $this->assertSame(Constants::AMQP_DURABLE, $queue->getFlags());
    }

    /**
     * @return string
     */
    private function generateName(): string
    {
        return uniqid('amqp', true);
    }
}
