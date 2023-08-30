<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data;

use function Misterx\DoctrineJmix\Doctrine\str_starts_with;

final class FetchPlanCalculatorResult
{
    private ?string $targetProperty = null;

    /**
     * @param array $select
     * @param class-string $from
     * @param array $join
     */
    public function __construct(private array $select, private string $from, private array $join){

    }

    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return class-string
     */
    public function getFrom():string
    {
        return $this->from;
    }

    public function getJoin():array
    {
        return $this->join;
    }

    public function setTargetProperty(string $targetProperty)
    {
        $this->targetProperty = $targetProperty;
    }

    public function toString():string
    {
        $select = array_map([$this,'stripTarget'],$this->select);
        $join = array_map([$this,'stripTarget'],$this->join);
        $from = array_reverse(explode('_',$this->from))[0];
        return 'SELECT '.implode(', ',$select).' FROM '.$from.
            implode(array_map(fn(string $joinRel)=>' JOIN '.$joinRel,$join)).
            ($this->targetProperty?' --> '.$this->targetProperty:'');

    }

    private function stripTarget(string $field):string
    {
        if(!$this->targetProperty){
            return $field;
        }
        if(str_starts_with($field,$this->targetProperty)){
            return substr($field,strlen($this->targetProperty)+1);
        }
        return $field;
    }
}