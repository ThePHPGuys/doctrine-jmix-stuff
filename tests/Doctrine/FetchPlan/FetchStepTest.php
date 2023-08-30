<?php

namespace Misterx\DoctrineJmix\Tests\Doctrine\FetchPlan;

use Misterx\DoctrineJmix\Declined\Data\FetchPlan\ArrayKeyExtractor;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\DataLoader;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\DefaultDataMapper;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\FetchStep;
use PHPUnit\Framework\TestCase;

class FetchStepTest extends TestCase
{
    private function createClientsLoader(): DataLoader
    {
        $clients = [
            [
                'clientId' => 1,
            ],
            [
                'clientId' => 2,
            ],
            [
                'clientId' => 3,
            ],
        ];
        return new ArrayDataLoader($clients, 'clientId');
    }

    private function createOrderLoader(): DataLoader
    {
        $clients = [
            [
                'orderId' => 1,
                'clientId' => 1,
            ],
            [
                'orderId' => 2,
                'clientId' => 1,
            ],
            [
                'orderId' => 3,
                'clientId' => 3,
            ],
        ];
        return new ArrayDataLoader($clients, 'orderId');
    }

    private function createOrderLinesLoader(): DataLoader
    {
        $clients = [
            [
                'orderLineId' => 1,
                'orderId' => 1,
            ],
            [
                'orderLineId' => 2,
                'orderId' => 1,
            ],
            [
                'orderLineId' => 3,
                'orderId' => 2,
            ],
            [
                'orderLineId' => 4,
                'orderId' => 2,

            ],
        ];
        return new ArrayDataLoader($clients, 'orderId', true);
    }

    public function testClientsLoader()
    {
        $clientsLoader = $this->createClientsLoader();
        $data = $clientsLoader->load([2]);
        $this->assertEquals([2 => ['clientId' => 2]], $data);
        $data = $clientsLoader->load([2, 1]);
        $this->assertEquals([2 => ['clientId' => 2], 1 => ['clientId' => 1]], $data);
        $data = $clientsLoader->load([5]);
        $this->assertEquals([], $data);
    }

    public function testOrderLines()
    {
        $orderLinesLoader = $this->createOrderLinesLoader();
        $orderLines = $orderLinesLoader->load([2]);
        $this->assertEquals([
            2 => [
                [
                    'orderLineId' => 3,
                    'orderId' => 2,
                ],
                [
                    'orderLineId' => 4,
                    'orderId' => 2,

                ]
            ]
        ],
            $orderLines
        );
    }

    public function testStep2One()
    {
        $orders = [
            [
                'orderId' => 1,
                'client' => 1,
            ],
            [
                'orderId' => 2,
                'client' => 1,
            ],
            [
                'orderId' => 3,
                'client' => 3,
            ],
        ];

        $clientStep = new FetchStep(new ArrayKeyExtractor('client'), $this->createClientsLoader(), new DefaultDataMapper('client'));

        $result = $clientStep->execute($orders);
        $expected = [
            [
                'orderId' => 1,
                'client' => [
                    'clientId' => 1,
                ],
            ],
            [
                'orderId' => 2,
                'client' => [
                    'clientId' => 1,
                ],
            ],
            [
                'orderId' => 3,
                'client' => [
                    'clientId' => 3,
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testStep2Many()
    {
        $orders = [
            [
                'orderId' => 1,
                'client' => 1,
            ],
            [
                'orderId' => 2,
                'client' => 1,
            ],
            [
                'orderId' => 3,
                'client' => 3,
            ],
        ];

        $oderLinesStep = new FetchStep(new ArrayKeyExtractor('orderId'), $this->createOrderLinesLoader(), new DefaultDataMapper('orderLines', []));

        $result = $oderLinesStep->execute($orders);
        $expected = [
            [
                'orderId' => 1,
                'orderLines' => [
                    [
                        'orderLineId' => 1,
                        'orderId' => 1,
                    ],
                    [
                        'orderLineId' => 2,
                        'orderId' => 1,
                    ],
                ],
                'client' => 1,
            ],
            [
                'orderId' => 2,
                'client' => 1,
                'orderLines' => [
                    [
                        'orderLineId' => 3,
                        'orderId' => 2,
                    ],
                    [
                        'orderLineId' => 4,
                        'orderId' => 2,

                    ]
                ],
            ],
            [
                'orderId' => 3,
                'orderLines' => [],
                'client' => 3,
            ],
        ];
        $this->assertEquals($expected, $result);
    }

}
