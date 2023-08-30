<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\FetchGroup;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;

final class OneToManyLoaderFactory
{
    public function __construct(private EntityManagerInterface $entityManager){

    }

    public function createLoader(array $assoc):callable
    {
        assert($assoc['type'] === ClassMetadataInfo::ONE_TO_MANY);
        $targetEntity = $assoc['targetEntity'];
        $mappedByField = $assoc['mappedBy'];
        $builder = $this->entityManager->createQueryBuilder()
            ->select(FetchGroupManager::ROOT_ALIAS)
            ->from($targetEntity,FetchGroupManager::ROOT_ALIAS)
            ->where(sprintf('%s.%s IN (:parentIds)',FetchGroupManager::ROOT_ALIAS,$mappedByField));
        return fn(array $ids)=>$builder->setParameter(':parentIds',$ids);
    }

    public function createNormalizedLoader(array $assoc, callable $executor):callable
    {
        assert($assoc['type'] === ClassMetadataInfo::ONE_TO_MANY);
        $targetEntity = $assoc['targetEntity'];
        $mappedByField = $assoc['mappedBy'];
        $builder = $this->entityManager->createQueryBuilder()
            ->select(FetchGroupManager::ROOT_ALIAS)
            ->from($targetEntity,FetchGroupManager::ROOT_ALIAS)
            ->where(sprintf('%s.%s IN (:parentIds)',FetchGroupManager::ROOT_ALIAS,$mappedByField));
        return fn(array $ids)=>$builder->setParameter(':parentIds',$ids);
    }


    /**
     * Creates normalizer for loader response
     * Normalizer should return array where key is parentId and value is related data
     *
     * @param array $assoc
     * @return callable
     */
    public function createNormalizer(array $assoc):callable
    {

    }
}