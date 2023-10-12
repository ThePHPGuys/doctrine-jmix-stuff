<?php

namespace TPG\PMix\Tests\Doctrine\Serializer;

use TPG\PMix\Doctrine\Serializer\Hydrator;
use PHPUnit\Framework\TestCase;
use TPG\PMix\Tests\Assets\NoRelationEntity;
use TPG\PMix\Tests\Assets\ToManyEntity;
use TPG\PMix\Tests\Assets\ToOneEntity;
use TPG\PMix\Tests\DoctrineMockTestCase;

class HydratorTest extends DoctrineMockTestCase
{
    private function getHydrator(): Hydrator
    {
        return new Hydrator($this->objectManager, $this->loadMetaData());
    }

    public function testHydrateLocalVariables()
    {
        $this->configureNoRelationEntity();

        $array = [
            'id' => 'a3c276c2-12cf-41c1-9d17-4e31e6c4282b',
            'field' => 'hydratedValue'
        ];
        /** @var NoRelationEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, NoRelationEntity::class);
        $this->assertEquals('hydratedValue', $entity->getField());
    }

    public function testHydrateLocalVariablesToExistingObject()
    {
        $this->configureNoRelationEntity();
        $entity = new NoRelationEntity();
        $entity->setId('existingId');

        $array = [
            'field' => 'hydratedValue'
        ];

        /** @var NoRelationEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, $entity);
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('existingId', $entity->getId());
    }

    public function testHydrateToOne()
    {
        $this->configureToOne();

        $array = [
            'id' => '12',
            'field' => 'hydratedValue',
            'toOne' => [
                'field' => 'HydratedToOne'
            ]
        ];
        /** @var ToOneEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, ToOneEntity::class);
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('HydratedToOne', $entity->getToOne()->getField());
    }

    public function testHydrateToOneExistingObject()
    {
        $this->configureToOne();

        $array = [
            'field' => 'hydratedValue',
            'toOne' => [
                'field' => 'HydratedToOne'
            ]
        ];
        $existingEntity = new ToOneEntity();
        $existingEntity->setId('toOneId');

        /** @var ToOneEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, $existingEntity);
        $this->assertEquals('toOneId', $entity->getId());
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('HydratedToOne', $entity->getToOne()->getField());
        $this->assertSame(spl_object_hash($existingEntity), spl_object_hash($entity));
    }


    public function testHydrateToOneLoadById()
    {
        $this->configureToOne();

        $existing = new NoRelationEntity();
        $existing->setId('nrExistingId');
        $existing->setField('existingField');
        $this->addExistingObject($existing);

        $array = [
            'id' => '12',
            'field' => 'hydratedValue',
            'toOne' => [
                'id' => 'nrExistingId'
            ]
        ];
        /** @var ToOneEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, ToOneEntity::class);
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('existingField', $entity->getToOne()->getField());
    }

    public function testHydrateToOneLoadByIdExistingObject()
    {
        $this->configureToOne();

        $existing = new NoRelationEntity();
        $existing->setId('nrExistingId');
        $existing->setField('existingField');
        $existing->setLoaded(true);
        $this->addExistingObject($existing);

        $array = [
            'field' => 'hydratedValue',
            'toOne' => [
                'id' => 'nrExistingId',
                'field' => 'newFieldValue'
            ]
        ];
        $baseEntity = new ToOneEntity();
        $baseEntity->setId('existingId');

        /** @var ToOneEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, $baseEntity);
        $this->assertEquals('existingId', $entity->getId());
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('newFieldValue', $entity->getToOne()->getField());
        $this->assertEquals(true, $entity->getToOne()->isLoaded());
    }

    public function testHydrateToMany()
    {
        $this->configureToMany();

        $array = [
            'id' => '12',
            'field' => 'hydratedValue',
            'entities' => [
                [
                    'field' => 'toMany1',
                ],
                [
                    'field' => 'toMany2',
                ]
            ]
        ];
        /** @var ToManyEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, ToManyEntity::class);
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('toMany1', $entity->getEntities()[0]->getField());
        $this->assertEquals('toMany2', $entity->getEntities()[1]->getField());
    }

    public function testHydrateToManyLoad()
    {
        $this->configureToMany();

        $existing = new NoRelationEntity();
        $existing->setId('nrExistingId');
        $existing->setField('existingField');
        $this->addExistingObject($existing);

        $array = [
            'id' => '12',
            'field' => 'hydratedValue',
            'entities' => [
                [
                    'field' => 'toMany1',
                ],
                [
                    'id' => 'nrExistingId',
                ]
            ]
        ];
        /** @var ToManyEntity $entity */
        $entity = $this->getHydrator()->hydrateEntity($array, ToManyEntity::class);
        $this->assertEquals('hydratedValue', $entity->getField());
        $this->assertEquals('toMany1', $entity->getEntities()[0]->getField());
        $this->assertEquals('existingField', $entity->getEntities()[1]->getField());
    }

}
