<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\Data;

use Misterx\DoctrineJmix\Doctrine\Data\JoinSelectConvertor;
use PHPUnit\Framework\TestCase;

class SelectConvertorTest extends TestCase
{
    public function testSimple()
    {
        $joins = [
            'category',
            'client.address',
            'client.address.city',
            'client',
            'category.name'
        ];

        $joinAliases = [];
        foreach ($joins as $i => $join) {
            $joinAliases[$join] = '_ja' . $i;
        }

        $forJoin = [];
        foreach ($joinAliases as $join => $alias) {
            [$parent, $field] = $this->getParentAndField($join);
            $parentAlias = $parent ? $joinAliases[$parent] : 'e';
            $forJoin[$alias] = $parentAlias . '.' . $field;
        }

        print_r($forJoin);
    }

    public function getParentAndField($path)
    {
        $parts = explode('.', strrev($path), 2);
        return [count($parts) == 2 ? strrev($parts[1]) : null, strrev($parts[0])];
    }


}
