<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\Data;

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Doctrine\Data\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\Data\QueryBuilderAssembler;
use Misterx\DoctrineJmix\Doctrine\Data\QueryBuilderAssemblerFactory;
use Misterx\DoctrineJmix\Doctrine\Data\QueryBuilderSortGenerator;
use Misterx\DoctrineJmix\Doctrine\MetaModel\DoctrineMetaDataTools;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use PHPUnit\Framework\TestCase;

class QueryBuilderAssemblerTest extends DoctrineTestCase
{
    protected EntityManagerInterface $em;
    protected QueryBuilderAssemblerFactory $assemblerFactory;

    protected function setUp(): void
    {
        $this->em = $this->getEntityManager();
        $metadata = $this->getOrdersMetadata($this->em);
        $metadataTools = new DoctrineMetaDataTools($this->em->getMetadataFactory());
        $aliasGenerator = new AliasGenerator();
        $sortGenerator = new QueryBuilderSortGenerator($aliasGenerator);
        $this->assemblerFactory = new QueryBuilderAssemblerFactory($metadata, $metadataTools, $sortGenerator);
        parent::setUp();
    }


    public function testAssembleWithEntityClassOnly()
    {
        $assembler = $this->assemblerFactory->create();
        $assembler->setEntityClass(Order::class);
        $assembler->setIds([1]);
        $assembler->setSort(Sort::by(\Misterx\DoctrineJmix\Data\Order::asc('name')));
        $queryBuilder = $assembler->assemble($this->em);
        echo $queryBuilder->getQuery()->getDQL();
    }
}
