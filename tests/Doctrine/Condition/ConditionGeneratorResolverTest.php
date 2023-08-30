<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\Condition;

use Misterx\DoctrineJmix\Data\Condition\LogicalCondition;
use Misterx\DoctrineJmix\Data\Condition\Operation;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGenerationContext;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;
use Misterx\DoctrineJmix\Doctrine\Condition\LogicalConditionGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\PropertyConditionGenerator;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
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
