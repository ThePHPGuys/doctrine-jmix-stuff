<?php
declare(strict_types=1);

namespace TPG\PMix\Data\Middleware;

use TPG\PMix\Data\DataContext;

interface Middleware
{
    /**
     * @param callable(DataContext):mixed $next
     */
    public function handle(DataContext $context, callable $next): mixed;
}