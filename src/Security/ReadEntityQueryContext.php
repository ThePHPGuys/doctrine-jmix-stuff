<?php
declare(strict_types=1);

namespace TPG\PMix\Security;

use TPG\PMix\Doctrine\QueryTransformer;
use TPG\PMix\MetaModel\MetaClass;

final class ReadEntityQueryContext implements AccessContext
{
    public function __construct(private MetaClass $metaClass, private QueryTransformer $queryTransformer)
    {

    }

    public function getMetaClass(): MetaClass
    {
        return $this->metaClass;
    }

    public function getQueryTransformer(): QueryTransformer
    {
        return $this->queryTransformer;
    }
}