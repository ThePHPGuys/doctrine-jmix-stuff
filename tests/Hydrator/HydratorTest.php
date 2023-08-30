<?php

namespace Misterx\DoctrineJmix\Tests\Hydrator;

use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\OrderLine;
use PHPUnit\Framework\TestCase;

class HydratorTest extends DoctrineTestCase
{
    public function testExtract()
    {
        $em = $this->getEntityManager();

        $order = new Order();
        $order->lines[] = new OrderLine();

    }


}
