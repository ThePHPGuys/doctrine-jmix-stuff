<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

final class FetchPlan
{
    /** @var FetchStep[]  */
    private $steps = [];

    public function __construct(FetchStep ...$steps)
    {
        $this->steps = $steps;
    }


    public function execute(array $initialData=[])
    {
        $currentData = $initialData;
        foreach ($this->steps as $step) {
            $currentData = $step->execute($currentData);
        }
        return $currentData;
    }

}