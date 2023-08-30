<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Middleware;

use Misterx\DoctrineJmix\Data\DataContext;

final class Next
{
    /** @var callable */
    private $default;

    public function __construct(private readonly \SplQueue $queue, callable $default)
    {
        $this->default = $default;
    }

    public function __invoke(DataContext $context)
    {
        if ($this->queue->isEmpty()) {
            return ($this->default)($context);
        }
        return $this->queue->dequeue()->handle(
            $context,
            fn(DataContext $newContext) => $this($newContext)
        );
    }
}