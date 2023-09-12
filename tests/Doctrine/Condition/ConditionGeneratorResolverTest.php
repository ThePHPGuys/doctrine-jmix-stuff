<?php

namespace TPG\PMix\Tests\Doctrine\Condition;

use TPG\PMix\Data\Condition\LogicalCondition;
use TPG\PMix\Data\Condition\Operation;
use TPG\PMix\Data\Condition\PropertyCondition;
use TPG\PMix\Doctrine\AliasGenerator;
use TPG\PMix\Doctrine\Condition\ConditionGenerationContext;
use TPG\PMix\Doctrine\Condition\ConditionGeneratorResolver;
use TPG\PMix\Doctrine\Condition\LogicalConditionGenerator;
use TPG\PMix\Doctrine\Condition\PropertyConditionGenerator;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use PHPUnit\Framework\TestCase;

class ConditionGeneratorResolverTest extends DoctrineTestCase
{
    private ConditionGeneratorResolver $resolver;
    private MetaData $metadata;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metadata = $this->getOrdersMetadata($this->getEntityManager());
        $resolver = $this->resolver = new ConditionGeneratorResolver();
        $resolver->addGenerator(new LogicalConditionGenerator($resolver));
        $resolver->addGenerator(new PropertyConditionGenerator($this->metadata, new AliasGenerator()));
    }


    public function testConditionsResolver()
    {
        $context = new ConditionGenerationContext(
            LogicalCondition::or(
                LogicalCondition::and(
                    PropertyCondition::createWithParameter('client.name', Operation::EQUAL, 'nameParam'),
                    PropertyCondition::createWithParameter('status', Operation::EQUAL, 'ageParameter')
                ),
                LogicalCondition::and(
                    PropertyCondition::createWithParameter('client.name', Operation::EQUAL, 'nameParam2'),
                    PropertyCondition::createWithParameter('status', Operation::IN_LIST, 'secondAgeParameter')
                ),
            )
        );
        $context->setEntityName($this->metadata->getByClass(Order::class)->getName());
        $context->setEntityAlias('e');
        $context->copyEntityValuesToChildContexts();

        $generator = $this->resolver->resolve($context);
        $joins = $generator->generateJoin($context);
        $this->assertSame([
            '_ja_client' => 'e.client'
        ], $joins);
        $where = $generator->generateWhere($context);
        $this->assertSame('( ( _ja_client.name = :nameParam AND e.status = :ageParameter ) OR ( _ja_client.name = :nameParam2 AND e.status IN (:secondAgeParameter) ) )', $where);


    }
}
