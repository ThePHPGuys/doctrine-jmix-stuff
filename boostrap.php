<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;
use Misterx\DoctrineJmix\Doctrine\Condition\LogicalConditionGenerator;
use Misterx\DoctrineJmix\Doctrine\Condition\PropertyConditionGenerator;
use Misterx\DoctrineJmix\QueryParamValuesManager;
use Psr\Log\AbstractLogger;

require_once "vendor/autoload.php";
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/src/Entity"),
    isDevMode: true,
);
$config->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware(new class extends AbstractLogger {
    public function log($level, $message, array $context = []): void
    {
        if ($level !== 'debug') {
            return;
        }
        dump($context);
    }
})]);
$connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
], $config);

$todayProvider = new class implements \Misterx\DoctrineJmix\QueryParamValueProvider {
    public function supports(string $parameterName): bool
    {
        return $parameterName === 'today';
    }

    public function getValue(mixed $value): mixed
    {
        return new DateTime();
    }

};
$currentUserProvider = new class implements \Misterx\DoctrineJmix\QueryParamValueProvider {
    public function supports(string $parameterName): bool
    {
        return $parameterName === 'currentUser';
    }

    public function getValue(mixed $value): mixed
    {
        return 'current_user_id';
    }

};
$initStart = microtime(true);
$doctrineStoreName = 'doctrine';
$entityManager = new EntityManager($connection, $config);
$doctrineMetadataFactory = $entityManager->getMetadataFactory();
$metaData = new \Misterx\DoctrineJmix\MetaModel\MetaData();
$doctrineLoader = new \Misterx\DoctrineJmix\Doctrine\DoctrineMetaDataLoader($doctrineMetadataFactory);
$classes = array_map(fn(\Doctrine\Persistence\Mapping\ClassMetadata $cmd) => $cmd->getName(), $doctrineMetadataFactory->getAllMetadata());
$doctrineLoader->load($classes, $metaData, $doctrineStoreName);
$metadataTools = new \Misterx\DoctrineJmix\MetaDataTools();
$accessManager = new \Misterx\DoctrineJmix\Security\AccessManager();
$viewBuilderFactory = new \Misterx\DoctrineJmix\ViewBuilderFactory($metaData, $metadataTools);
$viewsRepository = new \Misterx\DoctrineJmix\DefaultViewsRepository($metaData, $viewBuilderFactory);
$viewBuilderFactory->setRepository($viewsRepository);
$viewResolver = new \Misterx\DoctrineJmix\ViewResolver(
    $viewsRepository,
    $metaData
);
$aliasGenerator = new \Misterx\DoctrineJmix\Doctrine\AliasGenerator();
$queryBuilderSortProcessor = new \Misterx\DoctrineJmix\Doctrine\QuerySortProcessor($metaData, $aliasGenerator, $metadataTools);
$queryParamValuesProvider = new QueryParamValuesManager([$todayProvider, $currentUserProvider]);

$conditionResolver = new ConditionGeneratorResolver();
$conditionResolver->addGenerator(new LogicalConditionGenerator($conditionResolver));
$conditionResolver->addGenerator(new PropertyConditionGenerator($metaData, $aliasGenerator));
$queryConditionProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryConditionProcessor($conditionResolver);
$queryConditionParametersProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryConditionParametersProcessor($conditionResolver);
$queryViewProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryViewProcessor($metaData, $aliasGenerator);

$queryAssemblerFactory = new \Misterx\DoctrineJmix\Doctrine\QueryAssemblerFactory(
    $metaData,
    $metadataTools,
    $queryBuilderSortProcessor,
    $queryParamValuesProvider,
    $queryConditionProcessor,
    $queryConditionParametersProcessor,
    $queryViewProcessor,
);

$doctrineDataStore = new \Misterx\DoctrineJmix\Doctrine\DoctrineDataStore($entityManager, $queryAssemblerFactory, $viewsRepository, $accessManager);
$dataStores = new \Misterx\DoctrineJmix\Data\DataStores([
    $doctrineStoreName => $doctrineDataStore
]);
$dataManager = new \Misterx\DoctrineJmix\UnconstrainedDataManagerImpl($dataStores, $metaData);
$aliasGenerator = new \Misterx\DoctrineJmix\Doctrine\AliasGenerator();
//echo "Initialization took: " . (microtime(true) - $initStart) . " s" . PHP_EOL;
