<?php

namespace TPG\PMix\Tests\Doctrine\Data;

use Doctrine\ORM\EntityManagerInterface;
use TPG\PMix\Data\Sort;
use TPG\PMix\Doctrine\Data\AliasGenerator;
use TPG\PMix\Doctrine\Data\QueryBuilderAssembler;
use TPG\PMix\Doctrine\Data\QueryBuilderAssemblerFactory;
use TPG\PMix\Doctrine\Data\QueryBuilderSortGenerator;
use TPG\PMix\Doctrine\MetaModel\DoctrineMetaDataTools;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
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
        $assembler->setSort(Sort::by(\TPG\PMix\Data\Order::asc('name')));
        $queryBuilder = $assembler->assemble($this->em);
        echo $queryBuilder->getQuery()->getDQL();
    }
}
