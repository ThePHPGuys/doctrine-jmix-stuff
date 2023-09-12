<?php

namespace TPG\PMix\Tests\Doctrine;

use TPG\PMix\Data\Condition;
use TPG\PMix\Doctrine\AliasGenerator;
use TPG\PMix\Doctrine\Condition\ConditionGenerationContext;
use TPG\PMix\Doctrine\QueryConditionProcessor;
use TPG\PMix\Doctrine\QuerySortProcessor;
use TPG\PMix\MetaDataTools;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\Doctrine\Stubs\QueryTransformerStub;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use PHPUnit\Framework\TestCase;

class QueryConditionProcessorTest extends DoctrineTestCase
{
    private MetaData $metadata;
    private QueryConditionProcessor $queryConditionsProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metadata = $this->getOrdersMetadata($this->getEntityManager());
        $this->queryConditionsProcessor = new QueryConditionProcessor($this->createConditionGeneratorResolver($this->metadata));
    }

    /**
     * @dataProvider conditionsProvider
     */
    public function testConditionsGeneration(string $entityClass, Condition $condition, string $expectedWhere, array $expectedJoins)
    {
        $transformer = new QueryTransformerStub();
        $context = new ConditionGenerationContext($condition);
        $context->setEntityName($this->metadata->getByClass($entityClass)->getName());
        $this->queryConditionsProcessor->process($transformer, $context);
        $this->assertSame($expectedWhere, $transformer->getWhereString());
        $this->assertSame($expectedJoins, $transformer->getJoins());
    }

    public static function conditionsProvider(): \Generator
    {
        yield 'Simple condition' => [
            Order::class,
            Condition\PropertyCondition::createWithParameter('id', Condition\Operation::EQUAL, 'idParameter'),
            '{E}.id = :idParameter',
            []
        ];

        yield 'Join condition' => [
            Order::class,
            Condition\PropertyCondition::createWithParameter('client.name', Condition\Operation::EQUAL, 'name'),
            '_ja_client.name = :name',
            ['_ja_client' => '{E}.client']
        ];

        yield 'Join many condition' => [
            Order::class,
            Condition\PropertyCondition::createWithParameter('lines.product.name', Condition\Operation::EQUAL, 'p_name'),
            '_ja_lines_product.name = :p_name',
            [
                '_ja_lines' => '{E}.lines',
                '_ja_lines_product' => '_ja_lines.product'
            ]
        ];

        yield 'Join many logical condition' => [
            Order::class,
            Condition\LogicalCondition::and(
                Condition\PropertyCondition::createWithParameter('lines.product.name', Condition\Operation::EQUAL, 'p_name'),
                Condition\LogicalCondition::or(
                    Condition\PropertyCondition::createWithParameter('client.name', Condition\Operation::EQUAL, 'name'),
                    Condition\PropertyCondition::createWithParameter('client.name', Condition\Operation::EQUAL, 'name2'),
                )
            ),
            '( _ja_lines_product.name = :p_name AND ( _ja_client.name = :name OR _ja_client.name = :name2 ) )',
            [
                '_ja_lines' => '{E}.lines',
                '_ja_lines_product' => '_ja_lines.product',
                '_ja_client' => '{E}.client'
            ]
        ];

        yield 'Entity reference' => [
            Order::class,
            Condition\PropertyCondition::createWithParameter('client', Condition\Operation::EQUAL, 'clientId'),
            'IDENTITY({E}.client) = :clientId',
            []
        ];

        yield 'Entity 2many reference' => [
            Order::class,
            Condition\PropertyCondition::createWithParameter('lines.product', Condition\Operation::IN_LIST, 'productsId'),
            'IDENTITY(_ja_lines.product) IN (:productsId)',
            [
                '_ja_lines' => '{E}.lines'
            ]
        ];
    }
}
