<?php

namespace TPG\PMix\Tests\Doctrine\Condition;

use TPG\PMix\Data\Condition\Operation;
use TPG\PMix\Data\Condition\PropertyCondition;
use TPG\PMix\Doctrine\AliasGenerator;
use TPG\PMix\Doctrine\Condition\ConditionGenerationContext;
use TPG\PMix\Doctrine\Condition\PropertyConditionGenerator;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use PHPUnit\Framework\TestCase;

class PropertyConditionGeneratorTest extends DoctrineTestCase
{

    public function testGenerateJoin()
    {
        $metadata = $this->getOrdersMetadata($this->getEntityManager());
        $class = new PropertyConditionGenerator($metadata, new AliasGenerator());
        $context = new ConditionGenerationContext(PropertyCondition::equal('client.addresses.city', 'Bla'));
        $context->setEntityName($metadata->getByClass(Order::class)->getName());
        $joins = $class->generateJoin($context);
        $this->assertSame([
            "_ja_client" => "{e}.client",
            "_ja_client_addresses" => "_ja_client.addresses"
        ], $joins);
        $this->assertSame('_ja_client_addresses', $context->getJoinAlias());
        $this->assertSame('city', $context->getJoinProperty());
    }

    public function testGenerateJoin2()
    {
        $metadata = $this->getOrdersMetadata($this->getEntityManager());
        $class = new PropertyConditionGenerator($metadata, new AliasGenerator());
        $context = new ConditionGenerationContext(PropertyCondition::equal('client.actions.client', 'Bla'));
        $context->setEntityName($metadata->getByClass(Order::class)->getName());
        $joins = $class->generateJoin($context);
        $this->assertSame([
            "_ja_client" => "{e}.client",
            "_ja_client_actions" => "_ja_client.actions"
        ], $joins);
        $this->assertSame('_ja_client_actions', $context->getJoinAlias());
        $this->assertSame('client', $context->getJoinProperty());
    }

    public function testGenerateWhere()
    {
        $metadata = $this->getOrdersMetadata($this->getEntityManager());
        $class = new PropertyConditionGenerator($metadata, new AliasGenerator());
        $context = new ConditionGenerationContext(PropertyCondition::createWithParameter('client.name', Operation::EQUAL, 'param1'));
        $context->setEntityName($metadata->getByClass(Order::class)->getName());
        $class->generateJoin($context);
        $this->assertSame('_ja_client.name = :param1', $class->generateWhere($context));
    }
}
