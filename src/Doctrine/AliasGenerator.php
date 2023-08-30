<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

final class AliasGenerator
{
    private const JOIN_PREFIX = '_ja_';
    private const FIELD_PREFIX = '_fa_';
    private const DEFAULT_ROOT_ALIAS = '{RA}';
    /**
     * @var array<string,array{string,string}>
     */
    private array $joinCache = [];

    /**
     * @var array<string,array{string,string}>
     */
    private array $fieldsCache = [];

    /**
     * @var array<string,list<array{string,string}>>
     */
    private array $fieldJoinsCache = [];

    public function __construct()
    {

    }

    /**
     * Accept relation path and build join alias
     * Return array joinAlias, queryPath
     * @return array{string,string}
     */
    public function generateForJoin(string $joinPath, string $rootAlias = self::DEFAULT_ROOT_ALIAS): array
    {
        return $this->joinCache[$joinPath] ??= $this->generateForPath($joinPath, $rootAlias, self::JOIN_PREFIX);
    }

    /**
     * Return array selectAlias,queryField
     * @return array{string,string} Return [selectAlias, selectField]
     */
    public function generateForField(string $fieldPath, string $rootAlias = self::DEFAULT_ROOT_ALIAS): array
    {
        return $this->fieldsCache[$fieldPath] ??= $this->generateForPath($fieldPath, $rootAlias, self::FIELD_PREFIX);
    }

    /**
     * Generate joins list for field path
     * Example:
     * name -> has no joins
     * client.name -> has one join (e.client)
     * client.category.name -> has two joins (e.client , e.client.category)
     * @return list<array{string,string}>
     */
    public function generateJoinsForFieldPath(string $fieldPath, string $rootAlias = self::DEFAULT_ROOT_ALIAS): array
    {
        if (isset($this->fieldJoinsCache[$fieldPath])) {
            return $this->fieldJoinsCache[$fieldPath];
        }

        [$parent] = $this->getParentAndField($fieldPath);
        if (!$parent) {
            return [];
        }
        $parentParts = explode('.', $parent);
        $currentParts = [];
        $joins = [];
        foreach ($parentParts as $part) {
            $currentParts[] = $part;
            $joins[] = $this->generateForJoin(implode('.', $currentParts), $rootAlias);;
        }

        return $this->fieldJoinsCache[$fieldPath] = $joins;
    }

    private function generateForPath(string $path, string $rootAlias, string $prefix): array
    {
        [$parent, $field] = $this->getParentAndField($path);
        $parentAlias = $parent ? $this->generateForJoin($parent, $rootAlias)[0] : $rootAlias;

        return [$this->buildAlias($path, $prefix), sprintf('%s.%s', $parentAlias, $field)];
    }


    private function buildAlias(string $path, string $prefix = null): string
    {
        return ($prefix ?? '') . str_replace('.', '_', $path);
    }

    /**
     * Strip path, return last part of the pas as field and all parts before as parent
     * client.name => [client,name]
     * client.category.name => [client.category,name]
     * @param string $fieldPath
     * @return array{?string,string}
     */
    private function getParentAndField(string $fieldPath): array
    {
        $parts = explode('.', strrev($fieldPath), 2);
        return [count($parts) == 2 ? strrev($parts[1]) : null, strrev($parts[0])];
    }
}