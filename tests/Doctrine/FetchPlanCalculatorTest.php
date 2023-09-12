<?php

namespace TPG\PMix\Tests\Doctrine;

use TPG\PMix\Declined\Data\FetchPlanCalculator;
use TPG\PMix\Declined\Data\FetchPlanCalculatorResult;
use TPG\PMix\Declined\View;
use TPG\PMix\Doctrine\DoctrineMetaDataLoader;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Client;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use TPG\PMix\Tests\Entity\MetadataLoader\OrderLine;
use TPG\PMix\Tests\Entity\MetadataLoader\Product;
use TPG\PMix\Tests\Entity\MetadataLoader\Tag;

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
