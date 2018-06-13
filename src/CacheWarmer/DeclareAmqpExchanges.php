<?php

declare(strict_types=1);

namespace Amqp\CacheWarmer;

use Humus\Amqp\Exchange;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class DeclareAmqpExchanges implements CacheWarmerInterface
{
    /**
     * @var Exchange[]
     */
    private $exchanges;

    /**
     * @param Exchange[] $exchanges
     */
    public function __construct(Exchange ...$exchanges)
    {
        $this->exchanges = $exchanges;
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
        foreach ($this->exchanges as $exchange) {
            $exchange->declareExchange();
        }
    }
}
