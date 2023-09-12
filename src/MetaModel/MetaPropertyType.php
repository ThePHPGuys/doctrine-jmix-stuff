<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

enum MetaPropertyType
{
    case DATATYPE;
    case ENUM;
    case ASSOCIATION;
    case COMPOSITION;
    case EMBEDDED; //Not implemented
}