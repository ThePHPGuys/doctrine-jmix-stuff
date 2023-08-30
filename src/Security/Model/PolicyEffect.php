<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security\Model;

enum PolicyEffect: string
{
    case ALLOW = 'ALLOW';
    case DENY = 'DENY';
}