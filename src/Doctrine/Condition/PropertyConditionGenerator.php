<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Condition;

use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Data\Condition\ConditionUtils;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaProperty;

final readonly class PropertyConditionGenerator implements ConditionGenerator
{
    public function __construct(private MetaData $metaData, private AliasGenerator $aliasGenerator)
    {

    }

    public function supports(ConditionGenerationContext $context): bool
    {
        return $context->getCondition() instanceof PropertyCondition;
    }

    public function generateWhere(ConditionGenerationContext $context): string
    {
        $condition = $context->getCondition();
        assert($condition instanceof PropertyCondition);
        if ($context->getJoinAlias() && $context->getJoinProperty()) {
            $property = $this->getProperty($context->getJoinEntity(), $context->getJoinProperty());
            $isIdentity = ($property->getRange()->isClass() && !$property->getRange()->getCardinality()->isMany());
            return $this->buildWhere($condition, $context->getJoinAlias(), $context->getJoinProperty(), $isIdentity);
        } else {
            $property = $this->getProperty($context->getEntityName(), $condition->getProperty());
            $isIdentity = ($property->getRange()->isClass() && !$property->getRange()->getCardinality()->isMany());
            return $this->buildWhere($condition, $context->getEntityAlias(), $condition->getProperty(), $isIdentity);
        }
    }

    private function getProperty(string $entityName, string $property): MetaProperty
    {
        return $this->metaData->getByName($entityName)->getProperty($property);
    }

    public function generateJoin(ConditionGenerationContext $context): array
    {
        $condition = $context->getCondition();
        assert($condition instanceof PropertyCondition);
        if (!$context->getEntityName()) {
            return [];
        }
        $metaClass = $this->metaData->getByName($context->getEntityName());
        $propertyPath = $metaClass->getPropertyPath($condition->getProperty());
        if (!$propertyPath) {
            throw new \InvalidArgumentException('Invalid property path: ' . $condition->getProperty());
        }
        $propertyPathProperties = $propertyPath->getMetaProperties();
        if (count($propertyPathProperties) === 1) {
            //Own property then no joins
            return [];
        }

        $joins = $this->aliasGenerator->generateJoinsForFieldPath($condition->getProperty(), $context->getEntityAlias());
        [$joinAlias, $joinProperty] = explode('.', $this->aliasGenerator->generateForField($condition->getProperty(), $context->getEntityAlias())[1]);
        $context->setJoinProperty($joinProperty);
        $context->setJoinAlias($joinAlias);
        $context->setJoinEntity($propertyPath->getMetaProperty()->getMetaClass()->getName());
        return array_combine(array_column($joins, 0), array_column($joins, 1));
    }

    private function buildWhere(PropertyCondition $condition, string $entityAlias, string $property, bool $isIdentity): string
    {
        $whereProperty = sprintf('%s.%s', $entityAlias, $property);

        if ($isIdentity) {
            $whereProperty = 'IDENTITY(' . $whereProperty . ')';
        }

        if (ConditionUtils::isUnaryOperation($condition)) {
            return sprintf('%s %s',
                $whereProperty,
                DoctrineConditionUtils::getDQlOperation($condition)
            );
        }
        if (ConditionUtils::isCollectionOperation($condition)) {
            return sprintf('%s %s (:%s)',
                $whereProperty,
                DoctrineConditionUtils::getDQlOperation($condition),
                $condition->getParameterName()
            );
        }
        return sprintf('%s %s :%s',
            $whereProperty,
            DoctrineConditionUtils::getDQlOperation($condition),
            $condition->getParameterName()
        );
    }

    public function generateParameterValue(Condition $condition, mixed $parameterValue): mixed
    {
        assert($condition instanceof PropertyCondition);
        if ($parameterValue === null) {
            return null;
        }
        if (is_scalar($parameterValue)) {
            return match ($condition->getOperation()) {
                Condition\Operation::CONTAINS,
                Condition\Operation::NOT_CONTAINS => '%' . $parameterValue . '%',
                Condition\Operation::STARTS_WITH => $parameterValue . '%',
                Condition\Operation::ENDS_WITH => '%' . $parameterValue,
                default => $parameterValue
            };
        }
        return $parameterValue;
    }


}