<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Middleware;

use Misterx\DoctrineJmix\Data\DataContext;

interface Middleware
{
    /**
     * @param callable(DataContext):mixed $next
     */
    public function handle(DataContext $context, callable $next): mixed;
}