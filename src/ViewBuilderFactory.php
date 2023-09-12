<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\MetaModel\MetaData;

final class ViewBuilderFactory
{
    private ViewsRepository $viewsRepository;

    public function __construct(private readonly MetaData $metaData, private readonly MetaDataTools $metaDataTools)
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