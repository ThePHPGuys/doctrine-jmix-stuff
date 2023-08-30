<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine;

use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGenerationContext;
use Misterx\DoctrineJmix\Doctrine\QueryConditionProcessor;
use Misterx\DoctrineJmix\Doctrine\QuerySortProcessor;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\Doctrine\Stubs\QueryTransformerStub;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
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
