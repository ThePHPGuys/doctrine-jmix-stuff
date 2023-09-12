<?php

namespace TPG\PMix\Tests\Data\Condition;

use TPG\PMix\Data\Condition\ConditionUtils;
use TPG\PMix\Data\Condition\LogicalCondition;
use TPG\PMix\Data\Condition\PropertyCondition;
use PHPUnit\Framework\TestCase;

class ConditionUtilsTest extends TestCase
{

    public function testCollectNestedConditions()
    {
        $flatCond = [
            PropertyCondition::equal('a', 1),
            PropertyCondition::equal('b', 2),
            PropertyCondition::equal('c', 3),
            PropertyCondition::equal('d', 4),
        ];
        $cond = LogicalCondition::or(
            LogicalCondition::and($flatCond[0], $flatCond[1]),
            LogicalCondition::and($flatCond[2], $flatCond[3]),
        );
        $r2 = ConditionUtils::collectNestedConditions($cond, PropertyCondition::class);
        $this->assertSame($flatCond, $r2);
    }
}
