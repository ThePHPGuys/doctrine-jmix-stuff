<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;
use Misterx\DoctrineJmix\Doctrine\Condition\LogicalConditionGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\PropertyConditionGenerator;
use Misterx\DoctrineJmix\Doctrine\DoctrineMetaDataLoader;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Action;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Address;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Client;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\OrderLine;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Product;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Tag;
use PHPUnit\Framework\TestCase;

abstract class DoctrineTestCase extends TestCase
{
    private const ORDER_CLASSES = [Order::class, Client::class, OrderLine::class, Product::class, Tag::class, Address::class, Action::class];

    protected function getEntityManager(array $paths = [__DIR__ . "/Entity"]): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driverClass' => Mocks\ConnectionMock::class,
            'user' => 'john',
            'password' => 'wayne',
        ], $config);

        return new EntityManager($connection, $config);
    }

    protected function getRealEntityManager(array $paths = [__DIR__ . "/Entity"]): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driverClass' => Mocks\ConnectionMock::class,
            'user' => 'john',
            'password' => 'wayne',
        ], $config);

        return new EntityManager($connection, $config);
    }

    protected function loadMetaData(EntityManagerInterface $entityManager, array $classes): MetaData
    {
        $metadata = new MetaData();
        $metadataLoader = new DoctrineMetaDataLoader($entityManager->getMetadataFactory());
        $metadataLoader->load($classes, $metadata);
        return $metadata;
    }

    protected function getOrdersMetadata(EntityManagerInterface $entityManager): MetaData
    {
        return $this->loadMetaData($entityManager, self::ORDER_CLASSES);
    }

    protected function createConditionGeneratorResolver(MetaData $metaData): ConditionGeneratorResolver
    {
        $resolver = new ConditionGeneratorResolver();
        $resolver->addGenerator(new LogicalConditionGenerator($resolver));
        $resolver->addGenerator(new PropertyConditionGenerator($metaData, new AliasGenerator()));
        return $resolver;
    }

}