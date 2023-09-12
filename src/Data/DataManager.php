<?php
declare(strict_types=1);

namespace TPG\PMix\Data;

interface DataManager
{
    public function unconstrained(): UnconstrainedDataManager;
}