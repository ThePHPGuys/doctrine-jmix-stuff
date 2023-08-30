<?php

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Entity\Order;
use Misterx\DoctrineJmix\MetaModel\MetaData;

require 'boostrap.php';
/**
 * @var EntityManagerInterface $entityManager
 * @var MetaData $metaData
 */
$order = $entityManager->createQueryBuilder()
    ->select('o')
    ->from(Order::class, 'o')
    ->join('o.tags', 'tags')
    ->join('o.client', 'client')
    ->where('o.id IN (:id)')->setParameter('id', ['order641c4493aa1ad', 'order641c4493aa232'])
    ->getQuery()->execute();

dump($order);