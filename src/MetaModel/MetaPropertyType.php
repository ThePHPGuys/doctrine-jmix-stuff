<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel;

enum MetaPropertyType
{
    case DATATYPE;
    case ENUM;
    case ASSOCIATION;
    case COMPOSITION;
    case EMBEDDED; //Not implemented
}