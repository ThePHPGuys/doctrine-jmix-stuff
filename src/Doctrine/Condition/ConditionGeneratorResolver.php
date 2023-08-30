<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Condition;

final class ConditionGeneratorResolver
{
    /**
     * @var list<ConditionGenerator>
     */
    private array $conditionGenerators;

    public function addGenerator(ConditionGenerator $generator): void
    {
        $this->conditionGenerators[] = $generator;
    }

    public function resolve(ConditionGenerationContext $context): ConditionGenerator
    {
        foreach ($this->conditionGenerators as $generator) {
            if ($generator->supports($context)) {
                return $generator;
            }
        }
        throw new \LogicException('Condition generator not found');
    }
}