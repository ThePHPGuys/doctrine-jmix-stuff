<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

final class Key
{
    public function __construct(private mixed &$key){

    }
    public function getValue():mixed
    {
        return $this->key;
    }

    public function setValue(mixed $value):void
    {
        $this->key = $value;
    }
}