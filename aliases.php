<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Data\Condition\LogicalCondition;
use Misterx\DoctrineJmix\Data\Condition\Operation;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;
use Misterx\DoctrineJmix\Data\Order;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\QueryAssemblerFactory;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\UnconstrainedDataManagerImpl;
use Misterx\DoctrineJmix\ViewResolver;

require 'boostrap.php';
/**
 * @var EntityManagerInterface $entityManager
 * @var MetaData $metaData
 * @var MetaDataTools $metadataTools
 * @var ViewResolver $viewResolver
 * @var UnconstrainedDataManagerImpl $dataManager
 * @var QueryAssemblerFactory $queryAssemblerFactory
 * @var AliasGenerator $aliasGenerator
 */

//$sortProcessor = new \Misterx\DoctrineJmix\Doctrine\QueryBuilderSortProcessor($metaData,$aliasGenerator, $metadataTools);
//$qb = $entityManager->createQueryBuilder();
//$qb->select('e')
//    ->from(\Misterx\DoctrineJmix\Entity\Order::class,'e')
//;
//$sortProcessor->process($qb, Sort::by(Order::asc('amount'),Order::desc('client')),$metaData->getByClass(\Misterx\DoctrineJmix\Entity\Order::class)->getName());
//dump($qb->getQuery()->getDQL());
//dd($qb->getQuery()->getSQL());


$propertyCondition = LogicalCondition::or(PropertyCondition::equal('name', 'John'), PropertyCondition::equal('name', 'Jack'));

$missed = LogicalCondition::and($propertyCondition, PropertyCondition::equal('age', 45));
dump($missed);
