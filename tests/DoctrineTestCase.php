<?php
declare(strict_types=1);

namespace TPG\PMix\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use TPG\PMix\DefaultViewsRepository;
use TPG\PMix\Doctrine\AliasGenerator;
use TPG\PMix\Doctrine\Condition\ConditionGeneratorResolver;
use TPG\PMix\Doctrine\Condition\LogicalConditionGenerator;
use TPG\PMix\Doctrine\Condition\PropertyConditionGenerator;
use TPG\PMix\Doctrine\DoctrineMetaDataLoader;
use TPG\PMix\MetaDataTools;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\Entity\MetadataLoader\Action;
use TPG\PMix\Tests\Entity\MetadataLoader\Address;
use TPG\PMix\Tests\Entity\MetadataLoader\Client;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use TPG\PMix\Tests\Entity\MetadataLoader\OrderLine;
use TPG\PMix\Tests\Entity\MetadataLoader\Product;
use TPG\PMix\Tests\Entity\MetadataLoader\Tag;
use TPG\PMix\ViewBuilderFactory;
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


    protected function getViewBuilderFactory(MetaData $metaData): ViewBuilderFactory
    {
        $viewBuilderFactory = new ViewBuilderFactory($metaData, new MetaDataTools());
        $viewsRepository = new DefaultViewsRepository($metaData, $viewBuilderFactory);
        $viewBuilderFactory->setRepository($viewsRepository);
        return $viewBuilderFactory;
    }
}