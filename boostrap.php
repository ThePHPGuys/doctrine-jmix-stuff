<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;
use Misterx\DoctrineJmix\Doctrine\Condition\LogicalConditionGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\PropertyConditionGenerator;
use Misterx\DoctrineJmix\QueryParamValuesProvider;
use Psr\Log\AbstractLogger;

require_once "vendor/autoload.php";
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/src/Entity"),
    isDevMode: true,
);
$config->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware(new class extends AbstractLogger {
    public function log($level, $message, array $context = []): void
    {
        //print_r(func_get_args());
    }
})]);
$connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
], $config);
$doctrineStoreName = 'doctrine';
$entityManager = new EntityManager($connection, $config);
$doctrineMetadataFactory = $entityManager->getMetadataFactory();
$metaData = new \Misterx\DoctrineJmix\MetaModel\MetaData();
$doctrineLoader = new \Misterx\DoctrineJmix\Doctrine\DoctrineMetaDataLoader($doctrineMetadataFactory);
$classes = array_map(fn(\Doctrine\Persistence\Mapping\ClassMetadata $cmd) => $cmd->getName(), $doctrineMetadataFactory->getAllMetadata());
$doctrineLoader->load($classes, $metaData, $doctrineStoreName);
$metadataTools = new \Misterx\DoctrineJmix\MetaDataTools();
$viewBuilderFactory = new \Misterx\DoctrineJmix\ViewBuilderFactory($metaData, $metadataTools);
$viewsRepository = new \Misterx\DoctrineJmix\DefaultViewsRepository($metaData, $viewBuilderFactory);
$viewBuilderFactory->setRepository($viewsRepository);
$viewResolver = new \Misterx\DoctrineJmix\ViewResolver(
    $viewsRepository,
    $metaData
);
$aliasGenerator = new \Misterx\DoctrineJmix\Doctrine\AliasGenerator();
$queryBuilderSortProcessor = new \Misterx\DoctrineJmix\Doctrine\QuerySortProcessor($metaData, $aliasGenerator, $metadataTools);
$queryParamValuesProvider = new QueryParamValuesProvider();

$conditionResolver = new ConditionGeneratorResolver();
$conditionResolver->addGenerator(new LogicalConditionGenerator($conditionResolver));
$conditionResolver->addGenerator(new PropertyConditionGenerator($metaData, $aliasGenerator));
$queryConditionProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryConditionProcessor($conditionResolver);

$queryViewProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryViewProcessor($metaData, $aliasGenerator);

$queryAssemblerFactory = new \Misterx\DoctrineJmix\Doctrine\QueryAssemblerFactory(
    $metaData,
    $metadataTools,
    $queryBuilderSortProcessor,
    $queryParamValuesProvider,
    $queryConditionProcessor,
    $queryViewProcessor,
);

$doctrineDataStore = new \Misterx\DoctrineJmix\Doctrine\DoctrineDataStore($entityManager, $queryAssemblerFactory);
$dataStores = new \Misterx\DoctrineJmix\Data\DataStores([
    $doctrineStoreName => $doctrineDataStore
]);
$dataManager = new \Misterx\DoctrineJmix\UnconstrainedDataManagerImpl($dataStores);
$aliasGenerator = new \Misterx\DoctrineJmix\Doctrine\AliasGenerator();

