<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Data\ViewProperty;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;

final class ViewBuilder
{
    /** @var array<string,?View> */
    private array $properties = [];

    private MetaClass $metaClass;
    /**
     * @var true
     */
    private bool $systemProperties = false;
    /**
     * @var array<string,self>
     */
    private array $builders = [];

    public function __construct(private readonly MetaData $metaData, private readonly MetaDataTools $metaDataTools, private readonly ViewsRepository $viewsRepository, private readonly string $entityClass)
    {
        $this->metaClass = $this->metaData->getByClass($this->entityClass);
    }

    public function addView(string|View $view): self
    {
        if (is_string($view)) {
            return $this->addView($this->viewsRepository->getEntityView($this->entityClass, $view));
        }
        foreach ($view->getProperties() as $property) {
            $this->properties[$property->name] = $property->view;
        }
        return $this;
    }

    public function addSystem(): self
    {
        $this->systemProperties = true;
        return $this;
    }

    private function addSystemProperties(): void
    {
        $systemPropertiesNames = $this->metaDataTools->getSystemPropertyNames($this->metaClass);
        foreach ($systemPropertiesNames as $property) {
            if (array_key_exists($property, $this->properties)) {
                continue;
            }

            if ($this->metaClass->getProperty($property)->getRange()->isClass()) {
                $this->addProperty($property, View::INSTANCE_NAME);
            } else {
                $this->addProperty($property);
            }
        }
    }

    public function addProperty(string $property, ?string $viewName = null): self
    {
        $parts = explode('.', $property);
        $propName = $parts[0];
        $metaProperty = $this->metaClass->getProperty($propName);
        $this->properties[$propName] = null;
        if ($metaProperty->getRange()->isClass()) {
            if (!isset($this->builders[$propName])) {
                $className = $metaProperty->getRange()->asClass()->getClassName();
                $this->builders[$propName] = $this->createNestedBuilder($className);
            }
        }

        if (count($parts) === 1 && $viewName) {
            $this->builders[$propName]->addView($viewName);
            return $this;
        }

        if (count($parts) > 1) {
            if (!isset($this->builders[$propName])) {
                throw new \LogicException("Builder not found for property " . $propName);
            }
            $nestedBuilder = $this->builders[$propName];
            array_shift($parts);
            $nestedProp = implode('.', $parts);
            $nestedBuilder->addProperty($nestedProp, $viewName);
        }
        return $this;

    }

    /**
     * @param string $property
     * @param callable(self):void $nestedBuilder
     * @return $this
     */
    public function addPropertyBuilder(string $property, callable $nestedBuilder): self
    {
        $this->properties[$property] = null;
        $class = $this->metaClass->getProperty($property)->getRange()->asClass()->getClassName();
        $builder = $this->createNestedBuilder($class);
        $nestedBuilder($builder);
        $this->builders[$property] = $builder;
        return $this;
    }

    private function createNestedBuilder(string $entityClass): self
    {
        return new self($this->metaData, $this->metaDataTools, $this->viewsRepository, $entityClass);
    }

    public function build(): View
    {
        if ($this->systemProperties) {
            $this->addSystemProperties();
        }
        $viewProperties = [];
        foreach ($this->properties as $property => $propertyView) {
            if (isset($this->builders[$property])) {
                $viewProperties[] = new ViewProperty($property, $this->builders[$property]->build());
            } else {
                $viewProperties[] = new ViewProperty($property, $propertyView);
            }
        }
        return new View(properties: $viewProperties);
    }
}