<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine;

use Misterx\DoctrineJmix\Declined\Data\FetchPlanCalculator;
use Misterx\DoctrineJmix\Declined\Data\FetchPlanCalculatorResult;
use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\Doctrine\DoctrineMetaDataLoader;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Client;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\OrderLine;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Product;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Tag;

class FetchPlanCalculatorTest extends DoctrineTestCase
{
    private function loadMetadataFor(array $classes): MetaData
    {
        $doctrineMetadata = $this->getEntityManager([__DIR__ . '/../Entity/MetadataLoader'])->getMetadataFactory();
        $metadata = new MetaData();
        $metadataLoader = new DoctrineMetaDataLoader($doctrineMetadata);
        $metadataLoader->load($classes, $metadata);
        return $metadata;
    }

    public function testName()
    {
        $metadata = $this->loadMetadataFor([Order::class, OrderLine::class, Client::class, Product::class, Tag::class]);

        $calculator = new FetchPlanCalculator($metadata);

        $fetchGroup = new View();
        $fetchGroup->addAttribute('id');
        $fetchGroup->addAttribute('status');
        $fetchGroup->addAttribute('client.id');
        $fetchGroup->addAttribute('client.name');
        $fetchGroup->addAttribute('lines.id');
        $fetchGroup->addAttribute('lines.product.name');
        $fetchGroup->addAttribute('lines.product.tags.name');
        $result = $calculator->calculateAll($metadata->getByClass(Order::class), $fetchGroup);

        echo PHP_EOL;
        print_r(array_map(fn(FetchPlanCalculatorResult $result) => $result->toString(), $result));
    }

}
