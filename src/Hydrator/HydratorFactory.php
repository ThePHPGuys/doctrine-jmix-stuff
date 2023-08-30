<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Hydrator;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Laminas\Hydrator\Filter\PropertyName;
use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\Declined\ViewAttribute;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaProperty;
use Misterx\DoctrineJmix\MetaModel\RangeCardinality;

final class HydratorFactory
{
    public function __construct(private EntityManagerInterface $entityManager, private MetaData $metaData)
    {

    }

    public function createExtractor(View $view): DoctrineObject
    {
        $extractor = new DoctrineObject($this->entityManager);
        $viewProperties = array_map(fn(ViewAttribute $attribute) => $attribute->getName(), $view->getAttributes());
        $extractor->addFilter('allowProperties', new PropertyName($viewProperties, false));
        return $extractor;
    }

    public function createCollection(View $view): DoctrineObject
    {

        $extractor = new DoctrineObject($this->entityManager);
        $viewProperties = array_map(fn(ViewAttribute $attribute) => $attribute->getName(), $view->getAttributes());
        $extractor->addFilter('allowProperties', new PropertyName($viewProperties, false));
        return $extractor;
    }

    public function extract(object $object, View $view): array
    {
        $extractor = $this->createExtractor($view);
        $extractedData = $extractor->extract($object);
        $viewProperties = array_map(fn(ViewAttribute $attribute) => $attribute->getName(), $view->getAttributes());
        $metaProperties = $this->metaData->getByClass(ClassUtils::getClass($object))->getProperties();
        /** @var MetaProperty[] $usedAssociations */
        $usedAssociations = array_filter($metaProperties,
            fn(MetaProperty $property) => $property->getRange()->getCardinality() != RangeCardinality::NONE && in_array($property->getName(), $viewProperties));
        foreach ($usedAssociations as $association) {
            $associationView = $view->getAttribute($association->getName())->getView();
            if (!$associationView) {
                throw new \LogicException('Association view must be provided');
            }
            if (!$association->getRange()->getCardinality()->isMany()) {
                $extractedData[$association->getName()] = $this->extract($extractedData[$association->getName()], $associationView);
            } else {
                $extractedData[$association->getName()] = $this->extractCollection($extractedData[$association->getName()], $associationView);
            }
        }
        return $extractedData;
    }

    public function extractCollection(iterable $collection, View $view): array
    {
        $data = [];
        foreach ($collection as $key => $item) {
            $data[$key] = $this->extract($item, $view);
        }
        return $data;
    }

}