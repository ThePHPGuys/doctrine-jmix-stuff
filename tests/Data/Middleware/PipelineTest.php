<?php

namespace TPG\PMix\Tests\Data\Middleware;

use TPG\PMix\Data\DataContext;
use TPG\PMix\Data\Middleware\Middleware;
use TPG\PMix\Data\Middleware\Pipeline;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    public function testSimple()
    {
        $pipeline = new Pipeline();
        $pipeline->pipe(new Middleware1());
        $pipeline->pipe(new Middleware2());
        $result = $pipeline->handle(new ArrayContext(['context' => 'valueFromContext']), fn(DataContext $context) => ['default' => $context['context']]);
        $this->assertEquals(['default' => 'valueFromContext', 0 => Middleware2::class, 1 => Middleware1::class], $result);
    }

}

class ArrayContext extends \ArrayObject implements DataContext
{

}

class Middleware1 implements Middleware
{

    public function handle(DataContext $context, callable $next): mixed
    {
        $result = $next($context);
        assert(is_array($result));
        $result[] = __CLASS__;
        return $result;
    }

}

class Middleware2 implements Middleware
{

    public function handle(DataContext $context, callable $next): mixed
    {
        $result = $next($context);
        assert(is_array($result));
        $result[] = __CLASS__;
        return $result;
    }
}
