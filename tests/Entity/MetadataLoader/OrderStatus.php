<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Entity\MetadataLoader;

enum OrderStatus: string
{
    case NEW = 'new';
    case PAID = 'paid';
}