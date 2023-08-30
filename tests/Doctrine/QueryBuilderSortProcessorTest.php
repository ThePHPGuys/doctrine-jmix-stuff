<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine;

use Misterx\DoctrineJmix\Data\Order;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\QuerySortProcessor;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\Doctrine\Stubs\QueryTransformerStub;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order as OrderEntity;

class QueryBuilderSortProcessorTest extends DoctrineTestCase
{
    private MetaData $metadata;
    private QuerySortProcessor $sortProcessor;
    private QueryTransformerStub $queryTransformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metadata = $this->getOrdersMetadata($this->getEntityManager());
        $this->sortProcessor = new QuerySortProcessor($this->metadata, new AliasGenerator(), new MetaDataTools());
    }

    /**
     * @dataProvider sortProvider
     */
    public function testSort(string $entityClass, Sort $sort, array $expectedOrderBy = [], array $expectedJoins = [])
    {
        $ordersEntity = $this->metadata->getByClass($entityClass)->getName();
        $transformer = new QueryTransformerStub();
        $this->sortProcessor->process(
            $transformer,
            $sort,
            $ordersEntity
        );
        $this->assertSame($expectedOrderBy, $transformer->getOrderBy());
        $this->assertSame($expectedJoins, $transformer->getJoins());
    }

    public static function sortProvider(): \Generator
    {
        yield 'simple sort' => [
            OrderEntity::class,
            Sort::by(Order::asc('id')),
            ['{E}.id' => 'ASC'],
            []
        ];
        yield 'simple multiple sort' => [
            OrderEntity::class,
            Sort::by(Order::asc('id'), Order::desc('status')),
            ['{E}.id' => 'ASC', '{E}.status' => 'DESC'],
            []
        ];
        yield 'simple join sort' => [
            OrderEntity::class,
            Sort::by(Order::asc('client.name')),
            [
                '_ja_client.name' => 'ASC'
            ],
            [
                '_ja_client' => '{E}.client'
            ]
        ];
        yield 'skip m2m assoc' => [
            OrderEntity::class,
            Sort::by(Order::asc('client.addresses.city')),
            [],
            []
        ];
        yield 'combine join and simple' => [
            OrderEntity::class,
            Sort::by(Order::asc('client.name'), Order::desc('status')),
            [
                '_ja_client.name' => 'ASC',
                '{E}.status' => 'DESC'
            ],
            [
                '_ja_client' => '{E}.client'
            ]
        ];
    }

}
