<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final readonly class QueryBuilderTransformer
{
    public function __construct(private AliasGenerator $aliasGenerator)
    {

    }

    /**
     * @param QueryBuilder $builder
     * @param list<string> $joinPaths
     */
    public function addJoins(QueryBuilder $builder, array $joinPaths, string $rootAlias=null):void
    {
        foreach ($joinPaths as $joinPath){
            [$joinAlias,$join] = $this->aliasGenerator->generateForJoin($joinPath, $rootAlias);
            $builder->leftJoin($join,$joinAlias,Join::WITH);
        }
    }

    /**
     * @param QueryBuilder $builder
     * @param list<string> $selectPaths
     * @return array<string,string> Array of fields with aliases {resultAlias, fieldPath}
     */
    public function addSelects(QueryBuilder $builder, array $selectPaths,string $rootAlias=null):array
    {
        $resultMap = [];
        foreach ($selectPaths as $selectPath){
            [$selectAlias,$selectField] = $this->aliasGenerator->generateForField($selectPath, $rootAlias);
            $resultMap[$selectAlias] = $selectPath;
            $builder->addSelect(sprintf('%s AS %s',$selectField,$selectAlias));
        }
        return $resultMap;
    }
}