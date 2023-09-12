<?php

use Doctrine\ORM\EntityManagerInterface;
use TPG\PMix\Data\Condition\LogicalCondition;
use TPG\PMix\Data\Condition\Operation;
use TPG\PMix\Data\Condition\PropertyCondition;
use TPG\PMix\Data\LoadContext;
use TPG\PMix\Data\LoadContext\Query;
use TPG\PMix\Data\Order;
use TPG\PMix\Data\Sort;
use TPG\PMix\Data\View;
use TPG\PMix\Doctrine\QueryAssemblerFactory;
use TPG\PMix\Entity\Order as OrderEntity;
use TPG\PMix\MetaDataTools;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\UnconstrainedDataManagerImpl;
use TPG\PMix\ViewBuilderFactory;
use TPG\PMix\ViewResolver;

require 'boostrap.php';
/**
 * @var EntityManagerInterface $entityManager
 * @var MetaData $metaData
 * @var MetaDataTools $metadataTools
 * @var ViewResolver $viewResolver
 * @var UnconstrainedDataManagerImpl $dataManager
 * @var QueryAssemblerFactory $queryAssemblerFactory
 * @var ViewBuilderFactory $viewBuilderFactory
 */


////print_r($metaData->getClasses()[1]);
//$ordersMeta = $metaData->getByClass(Order::class);
////$view = $viewR->findMetaClassView($ordersMeta,\TPG\PMix\Data\View::LOCAL);
//$view = new \TPG\PMix\Data\View(\TPG\PMix\Data\View::LOCAL, [
//    new \TPG\PMix\Data\ViewProperty('client', new \TPG\PMix\Data\View(\TPG\PMix\Data\View::LOCAL))
//]);
//
//$as = $queryAssemblerFactory->create();
//$as->setEntityName($ordersMeta->getName());
//$as->setSort(\TPG\PMix\Data\Sort::by(new \TPG\PMix\Data\Order('name', \TPG\PMix\Data\Direction::ASC)));
////$dataManager->loadList(new \TPG\PMix\Data\LoadContext($ordersMeta));
//dd($as->assembleQuery($entityManager)->getDQL());

$queryProcessStart = microtime(true);
$view = $viewBuilderFactory->create(OrderEntity::class)
    ->addView(View::LOCAL)
    ->addProperty('client', View::LOCAL)
    ->addProperty('client.orders', View::LOCAL)
    ->build();

$condition = LogicalCondition::or(
    PropertyCondition::equal('client.name', 'John'),
    PropertyCondition::createWithParameter('client', Operation::EQUAL, 'currentUser'),
    PropertyCondition::createWithParameter('createdAt', Operation::LESS, 'today'),
);
$sort = Sort::by(
    Order::desc('createdAt'),
    Order::asc('client.name')
);

$context = new LoadContext($metaData->getByClass(OrderEntity::class));
//$context->setView($view);
$context->setQuery(
    Query::create()
        ->setCondition($condition)
        ->setSort($sort)
        ->setLimit(2)
        ->setOffset(1)
);

$t = $dataManager->loadList($context);


//echo "Query processing took: " . (microtime(true) - $queryProcessStart) . ' s' . PHP_EOL;