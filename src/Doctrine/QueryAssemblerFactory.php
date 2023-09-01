<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\QueryParamValuesProvider;

final readonly class QueryAssemblerFactory
{
    public function __construct(
        private MetaData                 $metaData,
        private MetaDataTools            $metaDataTools,
        private QuerySortProcessor       $sortProcessor,
        private QueryParamValuesProvider $paramValuesProvider,
        private QueryConditionProcessor  $queryConditionProcessor,
        private QueryViewProcessor       $queryViewProcessor
    )
    {

    }

    public function create(): QueryAssembler
    {
        return new QueryAssembler(
            $this->metaData,
            $this->metaDataTools,
            $this->sortProcessor,
            $this->paramValuesProvider,
            $this->queryConditionProcessor,
            $this->queryViewProcessor
        );
    }
}