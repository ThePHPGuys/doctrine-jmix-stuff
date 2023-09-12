<?php
declare(strict_types=1);

namespace TPG\PMix\Data\Middleware;

use TPG\PMix\Data\DataContext;
use SplQueue;

class Pipeline implements Middleware
{
    /**
     * @var SplQueue<Middleware>
     */
    private SplQueue $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    public function pipe(Middleware $middleware)
    {
        $this->queue->enqueue($middleware);
    }

    /**
     * @inheritdoc
     */
    public function handle(DataContext $context, callable $next): mixed
    {
        return (new Next(clone $this->queue, $next))($context);
    }
}