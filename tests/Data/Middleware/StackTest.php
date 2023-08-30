<?php

namespace Misterx\DoctrineJmix\Tests\Data\Middleware;

use Misterx\DoctrineJmix\Data\DataContext;
use Misterx\DoctrineJmix\Data\Middleware\Pipeline;
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
