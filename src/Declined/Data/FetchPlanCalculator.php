<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data;

use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;

final class FetchPlanCalculator
{

    public function calculateAll(MetaClass $metaClass, View $group):array
    {
        [$select, $from, $join, $batches] = $this->processOwn($metaClass,$group);
        $result = [new FetchPlanCalculatorResult($select,$from,$join)];
        if($batches){
            $batchesResult = $this->processBatches($metaClass,$group,$batches);
            $result = [...$result, ...$batchesResult];
        }
        return $result;
    }

    public function calculateOwn(MetaClass $metaClass, View $group):FetchPlanCalculatorResult
    {
        [$select, $from, $join] = $this->processOwn($metaClass,$group);
        return new FetchPlanCalculatorResult($select,$from,$join);
    }

    /**
     * Recursive calculate batches
     * @param MetaClass $metaClass
     * @param View $group
     * @param array $batches
     * @return array
     */
    private function processBatches(MetaClass $metaClass, View $group, array $batches):array {
        $result = [];
        foreach ($batches as $batch){
            [$select, $from, $join, $innerBatches] = $this->calculateOwn(
                $metaClass->getPropertyPath($batch)->getMetaProperty()->getRange()->asClass(),
                $group->getAttribute($batch)->getView()
            );
            $batchResult = new FetchPlanCalculatorResult($select,$from,$join);
            $batchResult->setTargetProperty($batch);
            $result[] = $batchResult;
            if($innerBatches){
                $innerBatchedResult = $this->processBatches($metaClass,$group,$innerBatches);
                $result = [...$result, ...$innerBatchedResult];
            }
        }
        return $result;
    }

    private function processOwn(MetaClass $metaClass, View $group):array
    {
        $select = [];
        $from = $metaClass->getClassName();
        $join = [];
        $batch = [];
        foreach ($group->getAttributes() as $attribute){
            if(!$metaClass->hasProperty($attribute->getName())){
                continue;
            }
            $metaProperty = $metaClass->getProperty($attribute->getName());
            $metaRange = $metaProperty->getRange();
            if($metaRange->isClass()){
                if(!$metaRange->getCardinality()->isMany()){
                    $join[] = $attribute->getPath();
                    if($attributeGroup = $attribute->getView()){
                        [$innerSelect, , $innerJoin, $innerBatch] = $this->calculateOwn($metaRange->asClass(),$attributeGroup);
                        $select = [...$select, ...$innerSelect];
                        $join = [...$join,...$innerJoin];
                        $batch = [...$batch, ...$innerBatch];
                    }
                }else{
                    $batch[] = $attribute->getPath();
                }
            }else{
                $select[] = $attribute->getPath();
            }
        }
        return [$select, $from, $join, $batch];
    }

}