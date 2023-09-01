<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaProperty;
use Misterx\DoctrineJmix\MetaModel\MetaPropertyPath;

final class QueryViewProcessor
{
    public function __construct(private MetaData $metaData, private AliasGenerator $aliasGenerator)
    {

    }

    public function process(QueryTransformer $queryTransformer, View $view, string $entityName): void
    {
        $paths = $this->getPaths($view, $this->metaData->getByName($entityName));
        $joins = $this->extractJoins($paths);
        $select = [QueryTransformer::ALIAS_PLACEHOLDER];
        foreach ($joins as $alias => $join) {
            $queryTransformer->addJoin($join, $alias);
            $select[] = $alias;
        }
        $queryTransformer->replaceSelect($select);
    }

    private function getPaths(View $view, MetaClass $metaClass, ?MetaPropertyPath $parentProperty = null): array
    {
        $paths = [];
        foreach ($view->getProperties() as $property) {
            $metaProperty = $metaClass->getProperty($property->name);
            if (!$metaProperty->getRange()->isClass() || !$property->view) {
                continue;
            }

            $propertyPath = $parentProperty ? MetaPropertyPath::append($parentProperty, $metaProperty) : new MetaPropertyPath($metaClass, [$metaProperty]);
            $paths = [...$paths, $propertyPath, ...$this->getPaths($property->view, $metaProperty->getRange()->asClass(), $propertyPath)];
        }

        return $paths;
    }

    /**
     * @param MetaPropertyPath[] $paths
     */
    private function extractJoins(array $paths): array
    {
        $joins = array_map(fn(MetaPropertyPath $path) => $this->aliasGenerator->generateForJoin($path->getPathString(), QueryTransformer::ALIAS_PLACEHOLDER), $paths);
        return array_combine(array_column($joins, 0), array_column($joins, 1));
    }

}