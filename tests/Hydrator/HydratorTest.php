<?php

namespace TPG\PMix\Tests\Hydrator;

use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use TPG\PMix\Tests\Entity\MetadataLoader\OrderLine;
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
