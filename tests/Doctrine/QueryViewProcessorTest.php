<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Data\ViewProperty;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\QueryViewProcessor;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\Doctrine\Stubs\QueryTransformerStub;
use Misterx\DoctrineJmix\Tests\DoctrineTestCase;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use Misterx\DoctrineJmix\ViewBuilderFactory;
use PHPUnit\Framework\TestCase;

class QueryViewProcessorTest extends DoctrineTestCase
{
    private MetaData $metaData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metaData = $this->getOrdersMetadata($this->getEntityManager());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testViewProcess(View $view, string $entityClass, array $expectedSelect, array $expectedJoins)
    {
        $queryTransformer = new QueryTransformerStub();
        $viewProcessor = new QueryViewProcessor($this->metaData, new AliasGenerator());
        $viewProcessor->process($queryTransformer, $view, $this->metaData->getByClass($entityClass)->getName());
        $this->assertSame($queryTransformer->getSelect(), $expectedSelect);
        $this->assertSame($queryTransformer->getJoins(), $expectedJoins);
    }


    public static function dataProvider(): \Generator
    {
        yield 'Simple' => [
            new View(properties: [
                new ViewProperty('id')
            ]
            ),
            Order::class,
            ['{E}'],
            []
        ];

        yield 'Assoc without props' => [
            new View(properties: [
                new ViewProperty('id'),
                new ViewProperty('client'),
            ]
            ),
            Order::class,
            ['{E}'],
            []
        ];

        yield 'ToOneJoin' => [
            new View(properties: [
                new ViewProperty('id'),
                new ViewProperty('client',
                    new View(properties: [
                        new ViewProperty('name')
                    ])
                ),
            ]
            ),
            Order::class,
            ['{E}', '_ja_client'],
            [
                '_ja_client' => '{E}.client'
            ]
        ];

        yield 'ToManyJoins' => [
            new View(properties: [
                new ViewProperty('id'),
                new ViewProperty('lines',
                    new View(properties: [
                        new ViewProperty('product',
                            new View(properties: [
                                new ViewProperty('name')
                            ])
                        )
                    ])
                ),
            ]
            ),
            Order::class,
            ['{E}', '_ja_lines', '_ja_lines_product'],
            [
                '_ja_lines' => '{E}.lines',
                '_ja_lines_product' => '_ja_lines.product'
            ]
        ];

        yield 'All joins' => [
            new View(properties: [
                new ViewProperty('id'),
                new ViewProperty('client',
                    new View(properties: [
                        new ViewProperty('name')
                    ])
                ),
                new ViewProperty('lines',
                    new View(properties: [
                        new ViewProperty('product',
                            new View(properties: [
                                new ViewProperty('name')
                            ])
                        )
                    ])
                ),
            ]
            ),
            Order::class,
            ['{E}', '_ja_client', '_ja_lines', '_ja_lines_product'],
            [
                '_ja_client' => '{E}.client',
                '_ja_lines' => '{E}.lines',
                '_ja_lines_product' => '_ja_lines.product'
            ]
        ];
    }

}
