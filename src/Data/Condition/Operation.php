<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Condition;

enum Operation: string
{
    case EQUAL = '=';
    case IS_SET = 'is set';
    case IN_LIST = 'in list';
    case NOT_IN_LIST = 'not in list';
}