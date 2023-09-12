<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Condition;

use TPG\PMix\Data\Condition;

final class DQLCondition implements Condition
{
    private const PARAMETER_PATTERN = '|:([\w.$]+)|';
    private array $join = [];

    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    private string $where = '';

    /**
     * @param string $where
     * @param string|null $join - join format is property space alias, each join separated by comma. Example. {E}.client client_alias, client_alias.address some_address_alias
     */
    public function __construct(string $where, string $join = null)
    {
        $this->setWhere($where);
        if ($join) {
            $this->setJoinString($join);
        }
    }

    public function setJoinString(string $join)
    {
        $joins = explode(',', $join);
        $joinParts = array_map(fn(string $join) => explode(' ', $join, 2), array_map('trim', $joins));
        $this->join = array_combine(array_column($joinParts, 1), array_column($joinParts, 0));
    }

    /**
     * @param array<string,mixed> $values
     * @return void
     */
    public function setParametersValues(array $values): void
    {
        $this->parameters = [...$this->parameters, ...$values];
    }

    public function setWhere(string $where)
    {
        if ($this->where === $where) {
            return;
        }
        if ($this->where) {
            $this->removeParameters($this->where);
        }
        if ($where) {
            $this->parseParameters($where);
        }
        $this->where = $where;

    }

    private function parseParameters(string $string): void
    {
        preg_match_all(self::PARAMETER_PATTERN, $string, $result);
        foreach ($result[1] as $parameter) {
            $this->parameters[$parameter] = null;
        }
    }

    private function removeParameters(string $string): void
    {
        preg_match_all(self::PARAMETER_PATTERN, $string, $result);
        foreach ($result[1] as $parameter) {
            unset($this->parameters[$parameter]);
        }
    }

    public function getParameters(): array
    {
        return array_keys($this->parameters);
    }

    public function getJoin(): array
    {
        return $this->join;
    }

    public function actualize(array $actualParameters): ?Condition
    {
        foreach ($this->parameters as $key => $value) {
            //Check if condition parameters exists in actual parameter, and we do not have value for parameter
            if (!in_array($key, $actualParameters) && ($value === null || $value === [])) {
                return null;
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return 'join and where';
    }

}