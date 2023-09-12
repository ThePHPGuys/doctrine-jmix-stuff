<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use TPG\PMix\Doctrine\DoctrineMetaDataLoader;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\MetaModel\MetaPropertyType;
use TPG\PMix\MetaModel\RangeCardinality;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Associated;
use TPG\PMix\Tests\Entity\MetadataLoader\Client;
use TPG\PMix\Tests\Entity\MetadataLoader\InverseO2O;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use TPG\PMix\Tests\Entity\MetadataLoader\OrderLine;
use TPG\PMix\Tests\Entity\MetadataLoader\OwningO2O;
use TPG\PMix\Tests\Entity\MetadataLoader\Product;
use TPG\PMix\Tests\Entity\MetadataLoader\ScalarEntity;
use TPG\PMix\Tests\Entity\MetadataLoader\Tag;
use TPG\PMix\Tests\Entity\MetadataLoader\Unidirectional;
use TPG\PMix\Tests\Entity\SameEntityAssociation\Followers;
use TPG\PMix\Tests\Entity\SameEntityAssociation\User;

final class MetadataLoaderTest extends DoctrineTestCase
{
    private function createDatatypeManager(): EntityManagerInterface
    {
        return $this->getEntityManager([__DIR__ . '/../Entity/MetadataLoader']);
    }

    private function loadMetadataFor(array $classes): MetaData
    {
        $doctrineMetadata = $this->createDatatypeManager()->getMetadataFactory();
        $metadata = new MetaData();
        $metadataLoader = new DoctrineMetaDataLoader($doctrineMetadata);
        $metadataLoader->load($classes, $metadata);
        return $metadata;
    }

    public function testClass()
    {
        $metadata = $this->loadMetadataFor([ScalarEntity::class]);
        $metaClass = $metadata->getByClass(ScalarEntity::class);
        $this->assertEquals(ScalarEntity::class, $metaClass->getClassName());
    }

    public function testRequired()
    {
        $metadata = $this->loadMetadataFor([ScalarEntity::class]);
        $metaClass = $metadata->getByClass(ScalarEntity::class);
        $this->assertTrue($metaClass->hasProperty('stringFieldNullable'));
        $this->assertFalse($metaClass->getProperty('stringFieldNullable')->isRequired());
    }

    public function testRange()
    {
        $metadata = $this->loadMetadataFor([ScalarEntity::class]);
        $metaClass = $metadata->getByClass(ScalarEntity::class);
        $this->assertFalse($metaClass->getProperty('stringField')->getRange()->isClass());
        $this->assertTrue($metaClass->getProperty('stringField')->getRange()->isDatatype());
        $this->assertSame($metaClass->getProperty('stringField')->getRange()->getCardinality(), RangeCardinality::NONE);

    }


    public function testAssociationO2O()
    {
        $metadata = $this->loadMetadataFor([Unidirectional::class, Associated::class]);
        $metaClass = $metadata->getByClass(Unidirectional::class);
        $property = $metaClass->getProperty('oneToOne');
        $this->assertTrue($property->getRange()->isClass());
        $this->assertFalse($property->getRange()->isDatatype());
        $this->assertSame($property->getRange()->getCardinality(), RangeCardinality::ONE_TO_ONE);
        $this->assertSame(Associated::class, $property->getRange()->asClass()->getClassName());
    }

    public function testAssociationM2O()
    {
        $metadata = $this->loadMetadataFor([Unidirectional::class, Associated::class]);
        $metaClass = $metadata->getByClass(Unidirectional::class);
        $property = $metaClass->getProperty('manyToOne');
        $this->assertTrue($property->getRange()->isClass());
        $this->assertFalse($property->getRange()->isDatatype());
        $this->assertSame($property->getRange()->getCardinality(), RangeCardinality::MANY_TO_ONE);
    }

    public function testAssociationM2M()
    {
        $metadata = $this->loadMetadataFor([Unidirectional::class, Associated::class]);
        $metaClass = $metadata->getByClass(Unidirectional::class);
        $property = $metaClass->getProperty('manyToMany');
        $this->assertTrue($property->getRange()->isClass());
        $this->assertFalse($property->getRange()->isDatatype());
        $this->assertSame($property->getRange()->getCardinality(), RangeCardinality::MANY_TO_MANY);
    }

    public function testComposition()
    {
        $metadata = $this->loadMetadataFor([Order::class, OrderLine::class, Client::class, Product::class, Tag::class]);
        $metaClass = $metadata->getByClass(Order::class);
        $property = $metaClass->getProperty('lines');
        $this->assertTrue($property->getRange()->isClass());
        $this->assertFalse($property->getRange()->isDatatype());
        $this->assertSame($property->getRange()->getCardinality(), RangeCardinality::ONE_TO_MANY);
        $this->assertSame($property->getType(), MetaPropertyType::COMPOSITION);
    }

    public function testEnum()
    {
        $metadata = $this->loadMetadataFor([Order::class, OrderLine::class, Client::class, Product::class, Tag::class]);
        $metaClass = $metadata->getByClass(Order::class);
        $property = $metaClass->getProperty('status');
        $this->assertFalse($property->getRange()->isClass());
        $this->assertFalse($property->getRange()->isDatatype());
        $this->assertTrue($property->getRange()->isEnum());
    }


    public function testInverse()
    {
        $metadata = $this->loadMetadataFor([User::class, Followers::class]);
        $metaClass = $metadata->getByClass(User::class);
        $this->assertEquals('to', $metaClass->getProperty('followers')->getInverse()?->getName());
        $metaClass = $metadata->getByClass(User::class);
        $this->assertEquals('from', $metaClass->getProperty('following')->getInverse()?->getName());
    }
}