<?php

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @var EntityManagerInterface $entityManager
 */
require 'boostrap.php';


$loader = new Loader();
$loader->addFixture(new \Misterx\DoctrineJmix\Fixture\OrderClientsFixture());
$executor = new ORMExecutor($entityManager, new ORMPurger());
$executor->execute($loader->getFixtures());
