<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\MetaModel\MetaData;

readonly final class ViewBuilderFactory
{
    private ViewsRepository $viewsRepository;

    public function __construct(private MetaData $metaData, private MetaDataTools $metaDataTools)
    {

    }

    public function setRepository(ViewsRepository $viewsRepository): void
    {
        $this->viewsRepository = $viewsRepository;
    }

    public function create(string $entityClass): ViewBuilder
    {
        return new ViewBuilder($this->metaData, $this->metaDataTools, $this->viewsRepository, $entityClass);
    }
}