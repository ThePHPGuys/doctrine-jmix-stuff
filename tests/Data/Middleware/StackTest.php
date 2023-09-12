<?php

namespace TPG\PMix\Tests\Data\Middleware;

use TPG\PMix\Data\DataContext;
use TPG\PMix\Data\Middleware\Pipeline;
use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testName()
    {
        $stack = $this->createStack();
        $context = $this->createContext();
        $stack->next()->handle($context, $stack);

    }

    private function createStack(): Pipeline
    {

    }

    private function createContext(): DataContext
    {
    }

}
