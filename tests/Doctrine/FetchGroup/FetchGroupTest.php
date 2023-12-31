<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\FetchGroup;

use Misterx\DoctrineJmix\Doctrine\Data\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\FetchGroup\FetchGroup;
use Misterx\DoctrineJmix\Doctrine\FetchGroup\FetchGroupManager;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use PHPUnit\Framework\TestCase;

class FetchGroupTest extends DoctrineTestCase
{
    //This is native doctrine implementation

    private function exampleGroup()
    {
        $fetchGroup = new FetchGroup();
        $fetchGroup->addAttribute('id');
        $fetchGroup->addAttribute('status');
        $fetchGroup->addAttribute('client.id');
        $fetchGroup->addAttribute('client.name');
        $fetchGroup->addAttribute('client.addresses.city'); //o2m
        $fetchGroup->addAttribute('client.actions');//o2m
        $fetchGroup->addAttribute('lines.id');
        $fetchGroup->addAttribute('lines.product.id');
        $fetchGroup->addAttribute('lines.product.name');
        $fetchGroup->addAttribute('lines.product.tags.name');
        return $fetchGroup;
    }

    public function testInitialFG()
    {
        $em = $this->getEntityManager();
        $manager = new FetchGroupManager($em, new AliasGenerator());
        $builder = $em->createQueryBuilder()->select('e')->from(Order::class, 'e');
        $fetchGroup = $this->exampleGroup();
        $manager->execute($builder, $fetchGroup);
    }

    public function testDoctrineAssocSQL()
    {
        $em = $this->getEntityManager();

        $target = $em->getClassMetadata(Order::class)->getAssociationTargetClass('lines');
        $persister = $em->getUnitOfWork()->getEntityPersister($target);
        $sql = $persister->getSelectSQL([]);
        print_r($sql);
    }
}
