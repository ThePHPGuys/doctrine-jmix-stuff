<?php
declare(strict_types=1);

namespace TPG\PMix\Data\Condition;

enum Operation: string
{
    case EQUAL = 'equal';
    case NOT_EQUAL = 'not_equal';
    case GREATER = 'greater';
    case GREATER_OR_EQUAL = 'greater_or_equal';
    case LESS = 'less';
    case LESS_OR_EQUAL = 'less_or_equal';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case IS_SET = 'is set';
    case IN_LIST = 'in list';
    case NOT_IN_LIST = 'not in list';
}