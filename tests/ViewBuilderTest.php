<?php

namespace Misterx\DoctrineJmix\Tests;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\DefaultViewsRepository;
use Misterx\DoctrineJmix\Doctrine\AliasGenerator;
use Misterx\DoctrineJmix\Doctrine\QuerySortProcessor;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Order;
use Misterx\DoctrineJmix\Tests\Entity\MetadataLoader\Product;
use Misterx\DoctrineJmix\ViewBuilder;
use Misterx\DoctrineJmix\ViewBuilderFactory;
use Misterx\DoctrineJmix\ViewsRepository;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\s;

class ViewBuilderTest extends DoctrineTestCase
{
    private MetaData $metaData;
    private ViewBuilderFactory $viewBuilderFactory;


    protected function setUp(): void
    {
        parent::setUp();
        $this->metaData = $this->getOrdersMetadata($this->getEntityManager());
        $this->viewBuilderFactory = new ViewBuilderFactory($this->metaData, new MetaDataTools());
        $viewsRepository = new DefaultViewsRepository($this->metaData, $this->viewBuilderFactory);
        $this->viewBuilderFactory->setRepository($viewsRepository);
    }

    public function createBuilder(string $className): ViewBuilder
    {
        return $this->viewBuilderFactory->create($className);
    }

    public function testAddViewPath()
    {
        $builder = $this->createBuilder(Order::class);
        $builder->addView(View::LOCAL);
        $builder->addProperty('lines.product');
        $view = $builder->build();
        $this->assertSameArray(['id', 'status', 'lines'], $this->getProperties($view));
        $this->assertSameArray(['product'], $this->getProperties($view->getProperty('lines')->view));
        $this->assertEmpty($view->getProperty('lines')->view->getProperty('product')->view->getProperties());
    }

    public function testAddPropertyBuilder()
    {
        $view = $this->createBuilder(Order::class)
            ->addPropertyBuilder('lines', function (ViewBuilder $builder) {
                $builder->addPropertyBuilder('product', function (ViewBuilder $builder) {
                    $builder->addProperty('name');
                });
            })->build();
        $this->assertTrue($view->hasProperty('lines'));
        $this->assertTrue($view->getProperty('lines')->view?->hasProperty('product'));
        $this->assertTrue($view->getProperty('lines')->view?->getProperty('product')->view?->hasProperty('name'));
    }

    /**
     * @return string[]
     */
    private function getProperties(View $view): array
    {
        return array_keys($view->getProperties());
    }

    private function assertSameArray($expected, $actual)
    {
        sort($expected);
        sort($actual);
        $this->assertSame($expected, $actual);
    }
}
