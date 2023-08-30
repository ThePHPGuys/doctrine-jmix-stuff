<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

final readonly class FetchStep
{

    public function __construct(
        private KeyValueExtractor $sourceKeyExtractor,
        private DataLoader        $dataLoader,
        private DataMapper        $dataMapper)
    {
    }

    public function execute(array $sourceData):array
    {
        $keys = $this->extractKeyValuesFromSource($this->sourceKeyExtractor, $sourceData);
        $relatedData = $this->loadRelatedData($this->dataLoader, $keys);
        return $this->mapData($this->dataMapper, $keys, $sourceData, $relatedData);
    }

    private function extractKeyValuesFromSource(KeyValueExtractor $keyFetcher, array $data):array
    {
        return array_map(fn(mixed $row)=>$keyFetcher->getKey($row),$data);
    }

    private function loadRelatedData(DataLoader $fetcher, array $keys):array
    {
        return $fetcher->load($keys);
    }

    private function mapData(DataMapper $dataMapper, array $keys, array $data, array $relatedData):array
    {
        $mergedData = [];
        foreach ($data as $rowId => $row){
            $mergedData[] = $dataMapper->map($keys[$rowId],$row, $relatedData);
        }
        return $mergedData;
    }

}