<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use TPG\PMix\Data\Condition\ConditionUtils;
use TPG\PMix\Data\Condition\PropertyCondition;
use TPG\PMix\Doctrine\Condition\ConditionGenerationContext;
use TPG\PMix\Doctrine\Condition\ConditionGeneratorResolver;

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