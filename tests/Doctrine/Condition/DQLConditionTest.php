<?php

namespace TPG\PMix\Tests\Doctrine\Condition;

use TPG\PMix\Doctrine\Condition\DQLCondition;
use PHPUnit\Framework\TestCase;

class DQLConditionTest extends TestCase
{
    public function testParameterParsing()
    {
        $condition = new DQLCondition('a.b=:b and c<>:u');
        $this->assertSame(['b', 'u'], $condition->getParameters());
        $condition->setWhere('z.z=:z');
        $this->assertSame(['z'], $condition->getParameters());
    }

    public function testJoinParsing()
    {
        $condition = new DQLCondition('', '{E}.client client_alias, client_alias.relation someRelation');
        $this->assertSame([
            'client_alias' => '{E}.client',
            'someRelation' => 'client_alias.relation'
        ], $condition->getJoin());
    }

}
