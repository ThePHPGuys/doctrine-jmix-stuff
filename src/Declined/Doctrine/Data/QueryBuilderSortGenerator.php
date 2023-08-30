<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Direction;
use Misterx\DoctrineJmix\Data\Order;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaPropertyPath;

final class QueryBuilderSortGenerator
{
    public function __construct(private AliasGenerator $aliasGenerator)
    {

    }

    public function addSort(MetaClass $metaClass, QueryBuilder $builder, Sort $sort):void
    {
        foreach ($sort->getOrders() as $order){
            $propertyPath = $metaClass->getPropertyPath($order->property);
            if(!$propertyPath){
                throw new \InvalidArgumentException(sprintf("Could not resolve property path '%s' in '%s'",$order->property,$metaClass->getName()));
            }
            $this->addPropertyOrder($builder,$propertyPath,$order->direction);
        }
    }

    private function addPropertyOrder(QueryBuilder $builder, MetaPropertyPath $metaPropertyPath, Direction $order): void
    {
        if(!$this->isSingleValuePath($metaPropertyPath)){
            return;
        }
        if($metaPropertyPath->getMetaProperty()->getRange()->isDatatype() || $metaPropertyPath->getMetaProperty()->getRange()->isEnum()){
            $this->addDatatypeOrder($builder,$metaPropertyPath,$order);
        }else{
            //Here instanceName fields should be extracted and sorted by them (use DataTools::getInstanceNameRelatedProperties)
            throw new \Exception('Unimplemented sort by instanceName');
        }
    }

    private function addDatatypeOrder(QueryBuilder $builder, MetaPropertyPath $metaPropertyPath, Direction $order):void
    {
        $requiredJoins = $this->aliasGenerator->generateJoinsForFieldPath($metaPropertyPath->getPathString(),'e');
        $existingJoins = $builder->getAllAliases();
        foreach ($requiredJoins as [$alias,$join]){
            if(in_array($alias,$existingJoins)){
                continue;
            }
            $builder->leftJoin($join,$alias,Join::WITH);
        }
        [,$field] = $this->aliasGenerator->generateForField($metaPropertyPath->getPathString(),'e');
        $builder->addOrderBy($field, $order->value);
    }

    private function isSingleValuePath(MetaPropertyPath $path):bool
    {
        foreach ($path->getMetaProperties() as $property){
            if($property->getRange()->isClass() && $property->getRange()->getCardinality()->isMany()){
                return false;
            }
        }
        return true;
    }

}