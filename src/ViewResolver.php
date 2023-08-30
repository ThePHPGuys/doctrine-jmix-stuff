<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Data\ViewProperty;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;

final class ViewResolver
{
    public function __construct(
        private readonly ViewsRepository $viewsRepository,
        private readonly MetaData        $metaData
    )
    {

    }

    /**
     * Resolve view and fill needed fields
     * $ordersPlan = new View('_base'); -- provide name if fetch plan extends some stored view
     * $ordersPlan->addProperty('customer');
     * Now view contains only one property customer, after resolving
     * should contain all fields from _base and additionally customer property
     */
    public function resolve(MetaClass $metaClass, View $view): View
    {
        $propertiesToPlan = $view->name ? $this->viewsRepository->findMetaClassView($metaClass, $view->name)->getProperties() : [];

        foreach ($view->getProperties() as $property) {
            if (!$metaClass->hasProperty($property->name)) {
                throw new \LogicException('Unknown property ' . $property->name);
            }
            $metaProperty = $metaClass->getProperty($property->name);
            if ($metaProperty->getRange()->isClass() && $property->view) {
                $propertyToPlan = new ViewProperty($property->name, $this->resolve($metaProperty->getRange()->asClass(), $property->view));
            } else {
                $propertyToPlan = new ViewProperty($property->name);
            }
            $propertiesToPlan[$property->name] = $propertyToPlan;
        }
        return new View(properties: $propertiesToPlan);
    }
}