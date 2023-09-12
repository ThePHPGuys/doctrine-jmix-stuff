<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use Doctrine\ORM\QueryBuilder;
use TPG\PMix\Data\LoadContext\Query;

final class DoctrineContextQuery extends Query
{
    private ?QueryBuilder $queryBuilder = null;

    public function setQueryBuilder(QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }
}