<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use TPG\PMix\Data\Direction;
use TPG\PMix\Data\Sort;
use TPG\PMix\MetaDataTools;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\MetaModel\MetaPropertyPath;

final readonly class QuerySortProcessor
{
    public function __construct(private MetaData $metaData, private AliasGenerator $aliasGenerator, private MetaDataTools $metaDataTools)
    {
        //Metadata should exist, because it can be used when entity name is empty for KeyValue storage
    }

    public function process(QueryTransformer $queryTransformer, Sort $sort, string $entityName): void
    {
        if (!$sort->isSorted()) {
            return;
        }
        $metaClass = $this->metaData->getByName($entityName);
        /** @var list<array{MetaPropertyPath,Direction}> $sortProperties */
        $sortProperties = [];
        foreach ($sort->getOrders() as $order) {
            $metaProperty = $metaClass->getPropertyPath($order->property);
            if (!$metaProperty) {
                continue;
            }
            $sortProperties[] = [
                $metaProperty,
                $order->direction
            ];
        }
        $expressions = $this->getSortExpressions($sortProperties);
        $this->modifyQueryBuilder($queryTransformer, $expressions);
    }

    /**
     * @param array{array<string,Direction>,array<string,string>} $expressions
     * @return void
     */
    private function modifyQueryBuilder(QueryTransformer $queryTransformer, array $expressions): void
    {
        //Add joins
        [$orders, $joins] = $expressions;
        foreach ($joins as $alias => $join) {
            $queryTransformer->addJoin($join, $alias);
        }
        $queryTransformer->replaceOrderByExpressions($orders);
    }

    /**
     * @param list<array{MetaPropertyPath,Direction}> $propertiesWithDirection
     * @return list{array<string,Direction>, array<string,string>} Return sort strings and required joins
     */
    private function getSortExpressions(array $propertiesWithDirection): array
    {
        /**
         * @var MetaPropertyPath $propertyPath
         * @var Direction $direction
         */
        $propertiesOrders = [];
        $sortJoins = [];
        foreach ($propertiesWithDirection as [$propertyPath, $direction]) {
            if (!$this->isSortable($propertyPath)) {
                continue;
            }
            $metaProperty = $propertyPath->getMetaProperty();
            if ($metaProperty->isTransient()) {
                //TODO: Create sorting for transient properties by fetching DependOn
                continue;
            }

            if (!$metaProperty->getRange()->isClass()) {
                [$fieldOrder, $joins] = $this->getDatatypeSortExpression($propertyPath, $direction);
                $propertiesOrders = [...$propertiesOrders, ...$fieldOrder];
                $sortJoins = [...$sortJoins, ...$joins];
            } elseif (!$metaProperty->getRange()->getCardinality()->isMany()) {
                [$fieldsOrder, $joins] = $this->getEntitySortExpression($propertyPath, $direction);
                $propertiesOrders = [...$propertiesOrders, ...$fieldsOrder];
                $sortJoins = [...$sortJoins, ...$joins];
            }
        }
        return [$propertiesOrders, $sortJoins];
    }

    private function isSortable(MetaPropertyPath $metaPropertyPath): bool
    {
        //Allow sort only by single value fields
        $properties = $metaPropertyPath->getMetaProperties();
        foreach ($properties as $property) {
            if ($property->getRange()->isClass() && $property->getRange()->getCardinality()->isMany()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param MetaPropertyPath $metaPropertyPath
     * @param Direction $direction
     * @return list{array<string,Direction>, array<string,string>} - return sort expression and joins<alias,join>
     */
    private function getDatatypeSortExpression(MetaPropertyPath $metaPropertyPath, Direction $direction): array
    {
        $orderBy = $this->aliasGenerator->generateForField($metaPropertyPath->getPathString(), QueryTransformer::ALIAS_PLACEHOLDER)[1];
        $joins = $this->aliasGenerator->generateJoinsForFieldPath($metaPropertyPath->getPathString(), QueryTransformer::ALIAS_PLACEHOLDER);
        $joinsMap = array_combine(array_column($joins, 0), array_column($joins, 1));
        return [[$orderBy => $direction], $joinsMap];
    }

    private function getEntitySortExpression(MetaPropertyPath $propertyPath, Direction $direction): array
    {
        $sortStrings = [];
        $sortJoins = [];
        $properties = $this->metaDataTools->getInstanceNameRelatedProperties($propertyPath->getMetaProperty()->getRange()->asClass());
        foreach ($properties as $property) {
            if ($property->isTransient()) {
                continue;
            }
            $instanceNameProperty = MetaPropertyPath::append($propertyPath, $property);
            [$expression, $joins] = $this->getDatatypeSortExpression($instanceNameProperty, $direction);
            $sortStrings = [...$sortStrings, ...$expression];
            $sortJoins = [...$sortJoins, ...$joins];
        }
        return [$sortStrings, $sortJoins];
    }
}