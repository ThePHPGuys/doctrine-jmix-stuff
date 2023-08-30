<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

interface DataManager
{
    public function unconstrained(): UnconstrainedDataManager;
}