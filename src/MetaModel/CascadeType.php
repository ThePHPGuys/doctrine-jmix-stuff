<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

enum CascadeType: string
{
    case PERSIST = 'persist';
    case REMOVE = 'remove';
    case REFRESH = 'refresh';
    case MERGE = 'merge';
    case DETACH = 'detach';
}
