<?php

namespace TPG\PMix\MetaModel;

enum RangeCardinality
{
    case NONE;
    case ONE_TO_ONE;
    case MANY_TO_ONE;
    case ONE_TO_MANY;
    case MANY_TO_MANY;

    public function isMany(): bool
    {
        return in_array($this, [self::MANY_TO_MANY, self::ONE_TO_MANY]);
    }
}
