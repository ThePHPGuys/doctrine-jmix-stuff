<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Misterx\DoctrineJmix\Data\Condition\ConditionUtils;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGenerationContext;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;

final readonly class QueryConditionProcessor
{
    public function __construct(private ConditionGeneratorResolver $conditionGeneratorResolver)
    {

    }

    public function process(QueryTransformer $queryTransformer, ConditionGenerationContext $context): void
    {
        $context->setEntityAlias(QueryTransformer::ALIAS_PLACEHOLDER);
        $context->copyEntityValuesToChildContexts();

        $generator = $this->conditionGeneratorResolver->resolve($context);
        $joins = $generator->generateJoin($context);
        $where = $generator->generateWhere($context);

        foreach ($joins as $alias => $join) {
            $queryTransformer->addJoin($join, $alias);
        }

        if ($where) {
            $queryTransformer->addWhere($where);
        }
    }
}