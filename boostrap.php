<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use TPG\PMix\Doctrine\Condition\ConditionGeneratorResolver;
use TPG\PMix\Doctrine\Condition\LogicalConditionGenerator;
use TPG\PMix\Doctrine\Condition\PropertyConditionGenerator;
use TPG\PMix\QueryParamValuesManager;
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

$todayProvider = new class implements \TPG\PMix\QueryParamValueProvider {
    public function supports(string $parameterName): bool
    {
        return $parameterName === 'today';
    }

    public function getValue(mixed $value): mixed
    {
        return new DateTime();
    }

};
$currentUserProvider = new class implements \TPG\PMix\QueryParamValueProvider {
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
$metaData = new \TPG\PMix\MetaModel\MetaData();
$doctrineLoader = new \TPG\PMix\Doctrine\DoctrineMetaDataLoader($doctrineMetadataFactory);
$classes = array_map(fn(\Doctrine\Persistence\Mapping\ClassMetadata $cmd) => $cmd->getName(), $doctrineMetadataFactory->getAllMetadata());
$doctrineLoader->load($classes, $metaData, $doctrineStoreName);
$metadataTools = new \TPG\PMix\MetaDataTools();
$accessManager = new \TPG\PMix\Security\AccessManager();
$viewBuilderFactory = new \TPG\PMix\ViewBuilderFactory($metaData, $metadataTools);
$viewsRepository = new \TPG\PMix\DefaultViewsRepository($metaData, $viewBuilderFactory);
$viewBuilderFactory->setRepository($viewsRepository);
$viewResolver = new \TPG\PMix\ViewResolver(
    $viewsRepository,
    $metaData
);
$aliasGenerator = new \TPG\PMix\Doctrine\AliasGenerator();
$queryBuilderSortProcessor = new \TPG\PMix\Doctrine\QuerySortProcessor($metaData, $aliasGenerator, $metadataTools);
$queryParamValuesProvider = new QueryParamValuesManager([$todayProvider, $currentUserProvider]);

$conditionResolver = new ConditionGeneratorResolver();
$conditionResolver->addGenerator(new LogicalConditionGenerator($conditionResolver));
$conditionResolver->addGenerator(new PropertyConditionGenerator($metaData, $aliasGenerator));
$queryConditionProcessor = new \TPG\PMix\Doctrine\QueryConditionProcessor($conditionResolver);
$queryConditionParametersProcessor = new \TPG\PMix\Doctrine\QueryConditionParametersProcessor($conditionResolver);
$queryViewProcessor = new \TPG\PMix\Doctrine\QueryViewProcessor($metaData, $aliasGenerator);

$queryAssemblerFactory = new \TPG\PMix\Doctrine\QueryAssemblerFactory(
    $metaData,
    $metadataTools,
    $queryBuilderSortProcessor,
    $queryParamValuesProvider,
    $queryConditionProcessor,
    $queryConditionParametersProcessor,
    $queryViewProcessor,
);

$doctrineDataStore = new \TPG\PMix\Doctrine\DoctrineDataStore($entityManager, $queryAssemblerFactory, $viewsRepository, $accessManager);
$dataStores = new \TPG\PMix\Data\DataStores([
    $doctrineStoreName => $doctrineDataStore
]);
$dataManager = new \TPG\PMix\UnconstrainedDataManagerImpl($dataStores, $metaData);
$aliasGenerator = new \TPG\PMix\Doctrine\AliasGenerator();
//echo "Initialization took: " . (microtime(true) - $initStart) . " s" . PHP_EOL;
