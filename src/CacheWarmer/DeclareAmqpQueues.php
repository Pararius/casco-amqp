<?php

declare(strict_types=1);

namespace Amqp\CacheWarmer;

use Humus\Amqp\Queue;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class DeclareAmqpQueues implements CacheWarmerInterface
{
    /**
     * @var Queue[]
     */
    private $queues;

    /**
     * @param Queue[] $queues
     */
    public function __construct(Queue ...$queues)
    {
        $this->queues = $queues;
    }

    /**
     * @inheritdoc
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function warmUp($cacheDir): void
    {
        foreach ($this->queues as $queue) {
            $queue->declareQueue();
        }
    }
}
