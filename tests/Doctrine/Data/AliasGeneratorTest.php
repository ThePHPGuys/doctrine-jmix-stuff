<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\Data;

use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use PHPUnit\Framework\TestCase;

class AliasGeneratorTest extends TestCase
{
    public function testJoinRootAlias()
    {
        $generator = new AliasGenerator();
        [$alias, $join] = $generator->generateForJoin('client', 'e');
        $this->assertSame($alias, '_ja_client');
        $this->assertSame($join, 'e.client');
    }

    public function testJoinNestedTwoAlias()
    {
        $generator = new AliasGenerator();
        [$alias, $join] = $generator->generateForJoin('client.city');
        $this->assertSame($alias, '_ja_client_city');
        $this->assertSame($join, '_ja_client.city');
    }

    public function testJoinNestedThreeAlias()
    {
        $generator = new AliasGenerator();
        [$alias, $join] = $generator->generateForJoin('client.city.region');
        $this->assertSame($alias, '_ja_client_city_region');
        $this->assertSame($join, '_ja_client_city.region');
    }

    public function testFieldAlias()
    {
        $generator = new AliasGenerator();
        [$alias, $select] = $generator->generateForField('client.name');
        $this->assertSame($alias, '_fa_client_name');
        $this->assertSame($select, '_ja_client.name');
    }

    public function testRootFieldAlias()
    {
        $generator = new AliasGenerator();
        [$alias, $select] = $generator->generateForField('name', 'e');
        $this->assertSame($alias, '_fa_name');
        $this->assertSame($select, 'e.name');
    }

    /**
     * @dataProvider joinPathProvider
     * @param string $path
     * @param array $expected
     * @return void
     */
    public function testGenerateJoinsForFieldPath(string $path, array $expected)
    {
        $generator = new AliasGenerator();
        $generatedJoins = $generator->generateJoinsForFieldPath($path);
        $this->assertSame($expected, $generatedJoins);
    }

    public static function joinPathProvider(): array
    {
        return [
            [
                'client',
                []
            ],
            [
                'client.name',
                [
                    ['_ja_client', '{e}.client']
                ]
            ],
            [
                'client.category.name',
                [
                    ['_ja_client', '{e}.client'],
                    ['_ja_client_category', '_ja_client.category']
                ]
            ]
        ];
    }
}
