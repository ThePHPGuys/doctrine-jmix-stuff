<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;

final readonly class QueryAssemblerFactory
{
    public function __construct(private MetaData $metaData, private MetaDataTools $metaDataTools, private QuerySortProcessor $sortProcessor)
    {

    }

    public function create(): QueryAssembler
    {
        return new QueryAssembler($this->metaData, $this->metaDataTools, $this->sortProcessor);
    }
}