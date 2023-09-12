<?php

namespace TPG\PMix\Tests\Doctrine;

use TPG\PMix\Data\View;
use TPG\PMix\Data\ViewProperty;
use TPG\PMix\Doctrine\AliasGenerator;
use TPG\PMix\Doctrine\QueryViewProcessor;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\Doctrine\Stubs\QueryTransformerStub;
use TPG\PMix\Tests\DoctrineTestCase;
use TPG\PMix\Tests\Entity\MetadataLoader\Order;
use TPG\PMix\ViewBuilderFactory;
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
