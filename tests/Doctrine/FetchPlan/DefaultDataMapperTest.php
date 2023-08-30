<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\FetchPlan;

use Misterx\DoctrineJmix\Declined\Data\FetchPlan\DefaultDataMapper;
use PHPUnit\Framework\TestCase;

class DefaultDataMapperTest extends TestCase
{
    public function testSimple()
    {
        $mapper = new DefaultDataMapper('client');
        $clients = [
            1 => [
                'name' => 'Client'
            ]
        ];
        $initialData = [];
        $mapped = $mapper->map(1, $initialData, $clients);
        $this->assertSame(['client' => ['name' => 'Client']], $mapped);
    }

    public function testDefault()
    {
        $mapper = new DefaultDataMapper('client', ['name' => 'Default client']);
        $clients = [
            1 => [
                'name' => 'Client'
            ]
        ];
        $initialData = [];
        $mapped = $mapper->map(2, $initialData, $clients);
        $this->assertSame(['client' => ['name' => 'Default client']], $mapped);
    }

}
