<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Entity\Order;
use Misterx\DoctrineJmix\MetaModel\MetaData;

require 'boostrap.php';
/**
 * @var EntityManagerInterface $entityManager
 * @var MetaData $metaData
 */
$hydratorFactory = new \Misterx\DoctrineJmix\Hydrator\HydratorFactory($entityManager, $metaData);
//$order = $entityManager->find(Order::class,'order641c4493aa1ad');

$orderIds = $entityManager->createQueryBuilder()
    ->select('o.id')
    ->from(Order::class, 'o');

$qb = $entityManager->createQueryBuilder();
$order = $qb
    ->select('o, tags, client')
    ->from(Order::class, 'o')
    ->join('o.tags', 'tags')
    ->join('o.client', 'client')
    ->where($qb->expr()->exists('SELECT o_filter from ' . Order::class . ' o_filter where o_filter.id IN (:ids) LIMIT 1'))
    ->setParameter('ids', ['order641c4493aa1ad', 'order641c4493aa232'])
    ->getQuery()->execute();
$clientView = new \Misterx\DoctrineJmix\Declined\View();
$clientView->addAttribute('id');
$clientView->addAttribute('name');

$tagsView = new \Misterx\DoctrineJmix\Declined\View();
$tagsView->addAttribute('id');
$tagsView->addAttribute('name');
$view = new \Misterx\DoctrineJmix\Declined\View();
$view->addAttribute('id');
$view->addAttribute('createdAt');
$view->addAttribute('client')->setView($clientView);
$view->addAttribute('tags')->setView($tagsView);
dump($order);
$extractor = $hydratorFactory->extractCollection($order, $view);
print_r(json_encode($extractor));


