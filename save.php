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

$oe = new OrderEntity();
$cl = new \TPG\PMix\Entity\Client();
$cl->name = 'BlaBla';
$oe->client = $cl;

$dataManager->saveEntity($oe);