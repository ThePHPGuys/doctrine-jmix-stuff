<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Data\Condition\LogicalCondition;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\LoadContext\Query;
use Misterx\DoctrineJmix\Data\Order;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Doctrine\QueryAssemblerFactory;
use Misterx\DoctrineJmix\Entity\Order as OrderEntity;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\UnconstrainedDataManagerImpl;
use Misterx\DoctrineJmix\ViewBuilderFactory;
use Misterx\DoctrineJmix\ViewResolver;

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
////$view = $viewR->findMetaClassView($ordersMeta,\Misterx\DoctrineJmix\Data\View::LOCAL);
//$view = new \Misterx\DoctrineJmix\Data\View(\Misterx\DoctrineJmix\Data\View::LOCAL, [
//    new \Misterx\DoctrineJmix\Data\ViewProperty('client', new \Misterx\DoctrineJmix\Data\View(\Misterx\DoctrineJmix\Data\View::LOCAL))
//]);
//
//$as = $queryAssemblerFactory->create();
//$as->setEntityName($ordersMeta->getName());
//$as->setSort(\Misterx\DoctrineJmix\Data\Sort::by(new \Misterx\DoctrineJmix\Data\Order('name', \Misterx\DoctrineJmix\Data\Direction::ASC)));
////$dataManager->loadList(new \Misterx\DoctrineJmix\Data\LoadContext($ordersMeta));
//dd($as->assembleQuery($entityManager)->getDQL());


$view = $viewBuilderFactory->create(OrderEntity::class)
    ->addView(View::LOCAL)
    ->addProperty('client', View::LOCAL)
    ->addProperty('client.orders', View::LOCAL)
    ->build();

$condition = LogicalCondition::or(
    PropertyCondition::equal('client.name', 'John'),
    PropertyCondition::equal('client.name', 'Jack'),
    PropertyCondition::equal('client', 'someClientId'),
);
$sort = Sort::by(
    Order::desc('createdAt'),
    Order::asc('client.name')
);

$context = new LoadContext($metaData->getByClass(OrderEntity::class));
$context->setView($view);
$context->setQuery(
    Query::create()
        ->setCondition($condition)
        ->setSort($sort)
        ->setLimit(2)
        ->setOffset(1)
);

$dataManager->loadList($context);
