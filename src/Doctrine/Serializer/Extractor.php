<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Serializer;

use Doctrine\Common\Collections\Collection;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Persistence\ObjectManager;
use Laminas\Hydrator\ExtractionInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use Laminas\Hydrator\Strategy\HydratorStrategy;
use Laminas\Hydrator\Strategy\StrategyChain;
use Laminas\Hydrator\Strategy\StrategyEnabledInterface;
use TPG\PMix\Data\View;
use TPG\PMix\Doctrine\Serializer\Hydrator\Strategy\CollectionStrategyExtractor;
use TPG\PMix\Doctrine\Serializer\Hydrator\ViewFilter;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\MetaModel\MetaProperty;

final readonly class Extractor
{
    public function __construct(private ObjectManager $objectManager, private MetaData $metaData)
    {

    }

    private function addToOneStrategy(MetaProperty $metaProperty, ?View $view, StrategyEnabledInterface $extractor): void
    {
        if ($view && !$view->getProperty($metaProperty->getName())?->view) {
            $extractor->addStrategy(
                $metaProperty->getName(),
                new ClosureStrategy(fn(object $entity) => $entity->getId())
            );
        } else {
            //Add nested object hydrator
            $nestedClass = $metaProperty->getRange()->asClass()->getClassName();
            $extractor->addStrategy(
                $metaProperty->getName(),
                new HydratorStrategy(
                    $this->createEntityHydrator(
                        $nestedClass,
                        $view?->getProperty($metaProperty->getName())?->view
                    ),
                    $nestedClass
                )
            );
        }
    }

    private function addToManyStrategy(MetaProperty $metaProperty, ?View $view, StrategyEnabledInterface $extractor): void
    {
        if ($view && !$view->getProperty($metaProperty->getName())?->view) {
            $extractor->addStrategy(
                $metaProperty->getName(),
                new ClosureStrategy(fn(Collection $collection) => $collection->map(fn(object $entity) => $entity->getId())->toArray())
            );
        } else {
            $nestedClass = $metaProperty->getRange()->asClass()->getClassName();
            $collectionExtractorStrategy = new CollectionStrategyExtractor(
                $this->createEntityHydrator(
                    $nestedClass,
                    $view?->getProperty($metaProperty->getName())?->view
                ),
                $nestedClass
            );
            $extractor->addStrategy(
                $metaProperty->getName(),
                new StrategyChain([
                    new ClosureStrategy(fn(Collection $collection) => $collection->toArray()),
                    $collectionExtractorStrategy
                ])
            );
        }
    }

    private function prepareStrategies(string $class, ?View $view, StrategyEnabledInterface $extractor): void
    {
        $metaProperties = $this->metaData->getByClass($class)->getProperties();
        foreach ($metaProperties as $metaProperty) {
            if (!$metaProperty->getRange()->isClass()) {
                continue;
            }
            if (!$metaProperty->getRange()->getCardinality()->isMany()) {
                $this->addToOneStrategy($metaProperty, $view, $extractor);
            } else {
                $this->addToManyStrategy($metaProperty, $view, $extractor);
            }
        }
    }

    private function createEntityHydrator(string $class, ?View $view, array $options = []): HydratorInterface
    {
        $extractor = new DoctrineObject($this->objectManager);
        if ($view) {
            $extractor->addFilter('viewFilter', new ViewFilter($view));
        }
        $this->prepareStrategies($class, $view, $extractor);
        return $extractor;
    }

    public function extractEntity(object $entity, ?View $view = null, array $options = []): array
    {
        return $this->createEntityHydrator($entity::class, $view, $options)->extract($entity);
    }

    public function extractEntityCollection(array $entities, ?View $view, array $options = []): array
    {
        return array_map(fn(object $entity) => $this->extractEntity($entity, $view, $options), $entities);
    }
}
