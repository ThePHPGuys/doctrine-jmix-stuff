<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\LoadContext\AbstractQuery;
use Misterx\DoctrineJmix\Data\LoadContext\Query;

final class DoctrineORMLoadContextQuery extends AbstractQuery
{
    public function __construct(private readonly QueryBuilder $builder){

    }

    public function getBuilder():QueryBuilder
    {
        return $this->builder;
    }

    public function getQueryString(): string
    {
        return $this->builder->getQuery()->getSQL();
    }

}