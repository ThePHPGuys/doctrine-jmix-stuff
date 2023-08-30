<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Hydrator;

use Doctrine\Laminas\Hydrator\DoctrineObject;
use Laminas\Hydrator\AbstractHydrator;
use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\MetaModel\MetaData;

final class ExtractService
{
    public function __construct(private AbstractHydrator $hydrator, private MetaData $metaData)
    {
    }

    private function createExtractor(): DoctrineObject
    {
        $this->hydrator->
    }

    public function extract(object $entity, View $view)
    {

        $
    }

    public function createFilters(View $view, MetaData $metaData, string $className)
    {
        $metaClass = $metaData->findByClass($className);
        print_r($metaClass->getAttributes());
    }


}