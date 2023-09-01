<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\LoadContext\Query;

final class DoctrineContextQuery extends Query
{
    private ?QueryBuilder $queryBuilder = null;

    public function setQueryBuilder(QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }
}