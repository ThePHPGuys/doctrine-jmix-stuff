<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\FetchGroup;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Doctrine\Data\AliasGenerator;

final class FetchGroupManager
{
    public const ROOT_ALIAS = 'e';
    private ClassMetadataFactory $metadataFactory;

    public function __construct(private EntityManagerInterface $entityManager, AliasGenerator $aliasGenerator){
        $this->metadataFactory = $entityManager->getMetadataFactory();
    }

    public function execute(QueryBuilder $builder, FetchGroup $group){
        $entityClass = $builder->getRootEntities()[0];
        [$select, $joins,$batches] = $this->processOwn($entityClass, $group);
        print_r($builder->getQuery()->getSQL());
        print_r(array_column($batches,0));
        foreach ($batches as [,$loader]){
            $loader([]);
        }
    }

    /**
     * Collect curent single values properties and batches
     * @param string $entityClass
     * @param FetchGroup $group
     * @return array<string[],string[],string[]> - select, joins, batches
     */
    private function processOwn(string $entityClass, FetchGroup $group):array
    {
        $metadata = $this->metadataFactory->getMetadataFor($entityClass);
        assert($metadata instanceof ClassMetadataInfo);
        $select = $joins = $batches = [];

        foreach ($group->getAttributes() as $attribute){
            $attributeName = $attribute->getName();
            if($metadata->hasField($attributeName)){
                $select[] = $attribute->getPath();
                continue;
            }

            //Is association
            $attributeGroup = $attribute->getGroup();
            if(!$attributeGroup){
                //Relation fields must be specified
                continue;
            }

            if(!$metadata->hasAssociation($attributeName)){
                continue;
            }

            if($metadata->isCollectionValuedAssociation($attributeName)){
                //Should have :
                // Key field
                // Target field
                // Loader by key field
                $assoc = $metadata->associationMappings[$attributeName];
                $primaryKeyAttributeName = $this->getPrimaryKeyForClass($assoc['targetEntity']);
                $parent = $attribute->getParent();
                if(!$parent->hasAttribute($primaryKeyAttributeName)){
                    throw new \InvalidArgumentException(sprintf('Attribute "%s" must exists in "%s" group',$primaryKeyAttributeName,$parent->getPath()?:'{root}'));
                }
                $keyAttribute = $parent->getAttribute($primaryKeyAttributeName);
                $targetAttribute = $attribute;
                $batches[] = [$keyAttribute, $targetAttribute,$this->createToManyLoader($assoc, $attributeGroup->clone())];
                continue;
            }

            //Single valued association
            $joins[] = $attribute->getPath();
            [$nestedSelect, $nestedJoins, $nestedBatches] = $this->processOwn($metadata->getAssociationTargetClass($attributeName),$attributeGroup);
            if($nestedSelect) {
                $select = [...$select, ...$nestedSelect];
            }
            if($nestedJoins) {
                $joins = [...$joins, ...$nestedJoins];
            }
            if($nestedBatches) {
                $batches = [...$batches, ...$nestedBatches];
            }


        }
        return [$select,$joins,$batches];
    }

    private function createToManyLoader(array $assoc, FetchGroup $fetchGroup):callable
    {
        if($assoc['type']===ClassMetadataInfo::ONE_TO_MANY){
            $factory = new OneToManyLoaderFactory($this->entityManager);
            $loader = $factory->createLoader($assoc);
            return fn(array $ids)=>$this->execute($loader($ids),$fetchGroup);
        }else{
            $factory = new ManyToManyLoaderFactory($this->entityManager);
            $loader = $factory->createLoader($assoc);
            return fn(array $ids)=>$this->execute($loader($ids),$fetchGroup);
        }
    }

    private function getPrimaryKeyForClass(string $class):string
    {
        $ids = $this->metadataFactory->getMetadataFor($class)->getIdentifier();
        if (count($ids)>1){
            throw new \LogicException('Composite primary keys are not supported');
        }
        return $ids[0];
    }

    private function createBatchExecutor(FetchGroup $fetchGroup):callable
    {
        return fn(QueryBuilder $builder)=>$this->execute($builder,$fetchGroup);
    }

    public function getJoin()
    {

    }

    public function getSelect()
    {

    }
}