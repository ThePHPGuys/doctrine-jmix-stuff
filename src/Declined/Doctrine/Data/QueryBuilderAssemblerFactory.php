<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaDataTools;

final readonly class QueryBuilderAssemblerFactory
{
    public function __construct(
        private MetaData $metaData,
        private MetaDataTools $dataTools,
        private QueryBuilderSortGenerator $sortGenerator)
    {

    }

    public function create():QueryBuilderAssembler
    {
        return new QueryBuilderAssembler($this->metaData,$this->dataTools, $this->sortGenerator);
    }
}