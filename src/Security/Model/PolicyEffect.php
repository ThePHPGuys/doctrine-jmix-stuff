<?php
declare(strict_types=1);

namespace TPG\PMix\Security\Model;

enum PolicyEffect: string
{
    case ALLOW = 'ALLOW';
    case DENY = 'DENY';
}