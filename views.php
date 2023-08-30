<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Doctrine\QueryAssemblerFactory;
use Misterx\DoctrineJmix\Entity\Order;
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
 */


//print_r($metaData->getClasses()[1]);
$ordersMeta = $metaData->getByClass(Order::class);
//$view = $viewR->findMetaClassView($ordersMeta,\Misterx\DoctrineJmix\Data\View::LOCAL);
$view = new \Misterx\DoctrineJmix\Data\View(\Misterx\DoctrineJmix\Data\View::LOCAL, [
    new \Misterx\DoctrineJmix\Data\ViewProperty('client', new \Misterx\DoctrineJmix\Data\View(\Misterx\DoctrineJmix\Data\View::LOCAL))
]);

$as = $queryAssemblerFactory->create();
$as->setEntityName($ordersMeta->getName());
$as->setSort(\Misterx\DoctrineJmix\Data\Sort::by(new \Misterx\DoctrineJmix\Data\Order('name', \Misterx\DoctrineJmix\Data\Direction::ASC)));
//$dataManager->loadList(new \Misterx\DoctrineJmix\Data\LoadContext($ordersMeta));
dd($as->assembleQuery($entityManager)->getDQL());

