<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Data\Condition\LogicalCondition;
use Misterx\DoctrineJmix\Data\Condition\Operation;
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

$oe = new OrderEntity();
$cl = new \Misterx\DoctrineJmix\Entity\Client();
$cl->name = 'BlaBla';
$oe->client = $cl;

$dataManager->saveEntity($oe);