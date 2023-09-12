<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

enum OrderStatus: string
{
    case NEW = 'new';
    case PAID = 'paid';
}