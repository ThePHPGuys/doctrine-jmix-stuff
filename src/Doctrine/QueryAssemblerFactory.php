<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use TPG\PMix\MetaDataTools;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\QueryParamValuesManager;

final readonly class QueryAssemblerFactory
{
    public function __construct(
        private MetaData                          $metaData,
        private MetaDataTools                     $metaDataTools,
        private QuerySortProcessor                $sortProcessor,
        private QueryParamValuesManager           $paramValuesProvider,
        private QueryConditionProcessor           $queryConditionProcessor,
        private QueryConditionParametersProcessor $queryConditionParametersProcessor,
        private QueryViewProcessor                $queryViewProcessor
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
            $this->queryConditionParametersProcessor,
            $this->queryViewProcessor
        );
    }
}