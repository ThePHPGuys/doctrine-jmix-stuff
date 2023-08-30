<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\ClosureDataLoader;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\FetchPlan;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\FetchStep;
use Misterx\DoctrineJmix\Declined\Data\FetchPlanCalculator;
use Misterx\DoctrineJmix\Declined\Data\FetchPlanCalculatorResult;
use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;

final class QueryExecutor
{
    public const FETCH_GROUP_HINT = 'fetch_group_hint';

    public function __construct(private readonly string $rootAlias = 'e')
    {

    }

    public function execute(QueryBuilder $builder, MetaClass $rootMetaClass)
    {
        if(!$fetchGroup) {
            $builder->getQuery()->execute();
        }

    }

    private function createFetchPlan(QueryBuilder $builder, MetaClass $rootMetaClass, View $fetchGroup):FetchPlan
    {
        $calculator = new FetchPlanCalculator();
        $calculatedSteps = $calculator->calculateAll($rootMetaClass,$fetchGroup);
        $steps = [];
        foreach ($calculatedSteps as $i=>$calculatedStep){
            if($i===0){
                $steps[] = $this->createInitialStep($calculatedStep,$builder);
            }else {
                $steps[] = $this->createBatchStep($calculatedStep);
            }
        }
        return new FetchPlan(...$steps);
    }

    private function createBatchStep(FetchPlanCalculatorResult $result):FetchStep
    {

    }

    private function getQueryBuilderTransformer():QueryBuilderTransformer
    {
        return new QueryBuilderTransformer(new AliasGenerator());
    }

    private function createInitialStep(FetchPlanCalculatorResult $result, QueryBuilder $initial):FetchStep
    {
        $initial = clone $initial;
        $this->transformBuilder($initial,$result,$this->getQueryBuilderTransformer());
        $dataLoader = new ClosureDataLoader((fn() => $this->executeBuilder($initial))(...));
    }

    private function executeBuilder(QueryBuilder $builder):mixed
    {
        return $builder->getQuery()->execute();
    }

    private function transformBuilder(QueryBuilder $builder, FetchPlanCalculatorResult $result, QueryBuilderTransformer $transformer):void
    {
        $builder->resetDQLPart('select');
        $transformer->addSelects($builder,$result->getSelect());
        $transformer->addJoins($builder,$result->getJoin());
    }

    private function canApplyFetchGroup(QueryBuilder $builder):bool
    {
        //Can only be applied if select field is one and this one is root entity
        /** @var Select[] $select */
        $select = $builder->getDQLPart('select');
        if(count($select)!=1){
            return false;
        }
        if(count($select[0]->getParts())!=1){
            return false;
        }
        if(count($builder->getRootAliases())!=1){
            return false;
        }
        return $select[0]->getParts()[0] === $builder->getRootAliases()[0];
    }
}