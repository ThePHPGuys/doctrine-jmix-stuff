<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\FetchGroup;

use Misterx\DoctrineJmix\Doctrine\FetchGroup\FetchAttribute;
use Misterx\DoctrineJmix\Doctrine\FetchGroup\FetchGroup;
use PHPUnit\Framework\TestCase;

class FetchAttributeTest extends TestCase
{

    public function testGetValueReference()
    {
        $fetchGroup = new FetchGroup();
        $fetchGroup->addAttribute('id');
        $fetchGroup->addAttribute('client.id');
        $fetchGroup->addAttribute('client.group.id');

        $data = [
            'id' => 'someId',
            'client' => [
                'id' => 'clientId',
                'group' => [
                    'id' => 'clientGroupId'
                ]
            ],
        ];

        $refG = &$fetchGroup->getValueReference($data);
        $refG['testElement'] = 3;
        $this->assertEquals($refG['testElement'], $data['testElement']);


        $ref = &$fetchGroup->getAttribute('id')->getValueReference($data);
        $this->assertSame($data['id'], $ref);
        $ref = 'newId';
        $this->assertSame($data['id'], $ref);

        $clientRef = &$fetchGroup->getAttribute('client')->getValueReference($data);
        $this->assertEquals($data['client'], $clientRef);
        $clientRef['newAttr'] = true;
        $this->assertEquals($data['client'], $clientRef);

        $clientGroup = &$fetchGroup->getAttribute('client.group')->getValueReference($data);
        $this->assertSame($data['client']['group'], $clientGroup);
        $clientGroupId = &$fetchGroup->getAttribute('client.group.id')->getValueReference($data);
        $clientGroupId = 'newGroupId';
        $this->assertEquals($data['client']['group']['id'], $clientGroupId);
    }
}
