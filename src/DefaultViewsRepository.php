<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Data\ViewProperty;
use Misterx\DoctrineJmix\MetaModel\MetaClass;

readonly class DefaultViewsRepository implements ViewsRepository
{
    public function __construct(private MetaDataTools $metaDataTools)
    {
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
                throw new \LogicException('Unimplemented');
            case View::BASE:
                throw new \LogicException('Unimplemented');
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
        $properties = [];
        foreach ($metaClass->getProperties() as $metaProperty) {
            if (!$metaProperty->getRange()->isClass()) {
                $properties[$metaProperty->getName()] = new ViewProperty($metaProperty->getName());
            }
        }
        $properties = $this->addSystemProperties($metaClass, $properties);
        return new View(properties: $properties);
    }

    /**
     * @param array<string,ViewProperty> $properties
     * @return array<string,ViewProperty>
     */
    private function addSystemProperties(MetaClass $metaClass, array $properties): array
    {
        $systemPropertiesNames = $this->metaDataTools->getSystemPropertyNames($metaClass);
        foreach ($systemPropertiesNames as $property) {
            if (array_key_exists($property, $properties)) {
                continue;
            }
            if ($metaClass->getProperty($property)->getRange()->isClass()) {
                //TODO: Default should be View::INSTANCE_NAME for related properties
                $properties[$property] = new ViewProperty($property, new View(View::BASE));
            } else {
                $properties[$property] = new ViewProperty($property);
            }
        }
        return $properties;
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