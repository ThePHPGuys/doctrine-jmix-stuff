<?php

namespace TPG\PMix\Tests\Doctrine\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use TPG\PMix\Data\View;
use TPG\PMix\Data\ViewProperty;
use TPG\PMix\Doctrine\Serializer\Extractor;
use PHPUnit\Framework\TestCase;
use TPG\PMix\Tests\Assets\ToManyEntity;
use TPG\PMix\Tests\Assets\ToOneEntity;
use TPG\PMix\Tests\Assets\NoRelationEntity;
use TPG\PMix\Tests\DoctrineMockTestCase;

class ExtractorTest extends DoctrineMockTestCase
{

    private function getExtractor(): Extractor
    {
        return new Extractor($this->objectManager, $this->loadMetaData());
    }

    public function createNoRelationEntity(string $valuePostfix = null): NoRelationEntity
    {
        $entity = new NoRelationEntity();
        $entity->setField('fieldValue' . $valuePostfix);
        $entity->setId('idValue' . $valuePostfix);
        return $entity;
    }

    public function createToOneEntity(): ToOneEntity
    {
        $entity = new ToOneEntity();
        $entity->setId('idParentValue');
        $entity->setField('fieldParentValue');
        $entity->setToOne($this->createNoRelationEntity());
        return $entity;
    }

    public function createToManyEntity(): ToManyEntity
    {
        $entity = new ToManyEntity();
        $entity->setId('idParentValue');
        $entity->setField('fieldParentValue');
        $entity->addEntities(new ArrayCollection([
            $this->createNoRelationEntity('1'),
            $this->createNoRelationEntity('2')
        ]));
        return $entity;
    }

    public function testSimpleExtract()
    {
        $this->configureNoRelationEntity();

        $this->assertEquals(
            [
                'field' => 'fieldValue',
                'id' => 'idValue'
            ],
            $this->getExtractor()->extractEntity($this->createNoRelationEntity())
        );
    }

    public function testSimpleExtractFiltered()
    {
        $view = new View([new ViewProperty('id')]);
        $this->configureNoRelationEntity();
        $this->assertEquals(
            [
                'id' => 'idValue'
            ],
            $this->getExtractor()->extractEntity($this->createNoRelationEntity(), $view)
        );
    }

    public function testToOneSimpleExtract()
    {
        $this->configureToOne();
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'field' => 'fieldParentValue',
                'toOne' => [
                    'id' => 'idValue',
                    'field' => 'fieldValue',
                ]
            ],
            $this->getExtractor()->extractEntity($this->createToOneEntity())
        );
    }

    public function testToOneFilterNested()
    {
        $this->configureToOne();
        $view = new View([
            new ViewProperty('id'),
            new ViewProperty('toOne', new View([
                new ViewProperty('field')
            ])),
        ]);
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'toOne' => [
                    'field' => 'fieldValue',
                ]
            ],
            $this->getExtractor()->extractEntity($this->createToOneEntity(), $view)
        );
    }

    public function testToOneFilterIdOnly()
    {
        $this->configureToOne();
        $view = new View([
            new ViewProperty('id'),
            new ViewProperty('toOne'),
        ]);
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'toOne' => 'idValue'
            ],
            $this->getExtractor()->extractEntity($this->createToOneEntity(), $view)
        );
    }


    public function testToManySimple()
    {
        $this->configureToMany();
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'field' => 'fieldParentValue',
                'entities' => [
                    [
                        "id" => "idValue1",
                        "field" => "fieldValue1",
                    ],
                    [
                        "id" => "idValue2",
                        "field" => "fieldValue2",
                    ]
                ]
            ],
            $this->getExtractor()->extractEntity($this->createToManyEntity())
        );
    }


    public function testToManyFilteredSimple()
    {
        $this->configureToMany();
        $view = new View([
            new ViewProperty('id'),
            new ViewProperty('entities', new View(
                [new ViewProperty('id')]
            )),
        ]);
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'entities' => [
                    ['id' => "idValue1"],
                    ['id' => "idValue2"]
                ]
            ],
            $this->getExtractor()->extractEntity($this->createToManyEntity(), $view)
        );
    }

    public function testToManyFilteredIds()
    {
        $this->configureToMany();
        $view = new View([
            new ViewProperty('id'),
            new ViewProperty('entities'),
        ]);
        $this->assertEquals(
            [
                'id' => 'idParentValue',
                'entities' => ["idValue1", "idValue2"]
            ],
            $this->getExtractor()->extractEntity($this->createToManyEntity(), $view)
        );
    }
}
