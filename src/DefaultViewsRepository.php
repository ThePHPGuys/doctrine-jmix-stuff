<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\Data\View;
use TPG\PMix\Data\ViewProperty;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\MetaData;

readonly class DefaultViewsRepository implements ViewsRepository
{
    public function __construct(private MetaData $metaData, private ViewBuilderFactory $viewBuilderFactory)
    {

    }

    public function getEntityView(string $entityClass, string $name): View
    {
        return $this->getMetaClassView($this->metaData->getByClass($entityClass), $name);
    }

    public function getMetaClassView(MetaClass $metaClass, string $name): View
    {
        $view = $this->findMetaClassView($metaClass, $name);
        if (!$view) {
            throw new \RuntimeException(sprintf('Unable to find view "%s" for entity "%s', $name, $metaClass->getName()));
        }
        return $view;
    }


    public function findMetaClassView(MetaClass $metaClass, string $name): ?View
    {
        if (!$this->isDefaultView($name)) {
            return null;
        }
        switch ($name) {
            case View::LOCAL:
                return $this->createLocalView($metaClass);
            case View::INSTANCE_NAME:
                return $this->createLocalView($metaClass);
            case View::BASE:
                return $this->createLocalView($metaClass);
            default:
                return null;
        }
    }

    private function isDefaultView(string $name): bool
    {
        return in_array($name, [View::BASE, View::INSTANCE_NAME, View::LOCAL]);
    }


    private function createLocalView(MetaClass $metaClass): View
    {
        $builder = $this->viewBuilderFactory->create($metaClass->getClassName());
        $builder->addSystem();
        foreach ($metaClass->getProperties() as $metaProperty) {
            //TODO:
            if (!$metaProperty->getRange()->isClass() && !$metaProperty->isTransient()) {
                $builder->addProperty($metaProperty->getName());
            }
        }

        return $builder->build();
    }


    private function getInstanceNameView(View $view, MetaClass $metaClass): View
    {
        $properties = [];
        foreach ($metaClass->getProperties() as $metaProperty) {
            if (!$metaProperty->getRange()->isClass()) {
                $properties[] = new ViewProperty($metaProperty->getName());
            }
        }
        return new View(properties: $properties);
    }
}