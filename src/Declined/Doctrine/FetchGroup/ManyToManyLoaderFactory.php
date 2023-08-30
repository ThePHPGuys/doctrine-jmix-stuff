<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\FetchGroup;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final class ManyToManyLoaderFactory
{
    public function __construct(private EntityManagerInterface $entityManager){

    }

    public function createLoaderBuilder(array $assoc):callable
    {
        assert($assoc['type'] === ClassMetadataInfo::MANY_TO_MANY);
        $sourceEntity = $assoc['sourceEntity'];
        $rekKey = $assoc['isOwningSide'] === 1? 'joinColumns':'inverseJoinColumns';
        $refId = $assoc['joinTable'][$rekKey][0]['referencedColumnName'];
        $associationName = $assoc['fieldName'];
        $builder = $this->entityManager->createQueryBuilder()
            ->select('e',sprintf('PARTIAL s.{%s} HIDDEN',$refId))
            ->from($sourceEntity, 's','s.'.$refId)
            ->leftJoin(sprintf('s.%s',$associationName),'e',Join::WITH)
            ->where(sprintf('s.%s IN (:parentIds)',$refId));
        return fn(array $ids)=>$builder->setParameter(':parentIds',$ids);
    }

    /**
     * @param array $assoc
     * @param callable $executor
     * @return callable
     */
    public function createLoaderNormalized(array $assoc, callable $executor):callable
    {
        assert($assoc['type'] === ClassMetadataInfo::MANY_TO_MANY);
        $sourceEntity = $assoc['sourceEntity'];
        $rekKey = $assoc['isOwningSide'] === 1? 'joinColumns':'inverseJoinColumns';
        $refId = $assoc['joinTable'][$rekKey][0]['referencedColumnName'];
        $associationName = $assoc['fieldName'];
        $builder = $this->entityManager->createQueryBuilder()
            ->select('e',sprintf('PARTIAL s.{%s} HIDDEN',$refId))
            ->from($sourceEntity, 's','s.'.$refId)
            ->leftJoin(sprintf('s.%s',$associationName),'e',Join::WITH)
            ->where(sprintf('s.%s IN (:parentIds)',$refId));
        return fn(array $ids)=>$builder->setParameter(':parentIds',$ids);
    }

    /**
     * Creates normalizer for loader response
     * Normalizer should return array where key is parentId and value is related data
     * @param array $assoc
     * @return callable
     */
    public function createNormalizer(array $assoc):callable
    {

    }

}