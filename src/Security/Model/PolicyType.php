<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security\Model;

enum PolicyType: string
{
    case ENTITY = 'entity';
    case ENTITY_ATTRIBUTE = 'entity_attribute';
}