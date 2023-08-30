<?php

use Overblog\DataLoader\DataLoader;

require 'vendor/autoload.php';

$promiseAdapter = new \Overblog\PromiseAdapter\Adapter\ReactPromiseAdapter();

$myLinesLoader = function (array $ids) use ($promiseAdapter) {
    return $promiseAdapter->createFulfilled(
        [
            [
                ['name' => 'o1l1'],
                ['name' => 'o1l2']
            ],
            [
                ['name' => 'o2l1'],
                ['name' => 'o2l2']
            ]
        ]);
};

$myAddressLoader = function (array $ids) use ($promiseAdapter) {
    return $promiseAdapter->createFulfilled(
        [
            'address1',
            'address2'
        ]);
};
$initialData = [
    ['name' => 'Order1', 'id' => 1],
    ['name' => 'Order2', 'id' => 2],
];
$linesLoader = new DataLoader($myLinesLoader, $promiseAdapter);
$addrLoader = new DataLoader($myAddressLoader, $promiseAdapter);
$promises = [];

foreach ($initialData as $o) {
    $order = $promiseAdapter->createFulfilled($o);
    $lines = $order->then(
        function ($o) use ($linesLoader, $addrLoader, $promiseAdapter) {
            $ln = $linesLoader->load($o['id'])->then(fn($lines) => ['lines' => $lines]);
            $ad = $addrLoader->load($o['id'])->then(fn($addr) => ['ad' => $addr]);
            return $promiseAdapter->createAll([$o, $ln, $ad]);
        }
    )->then(function ($data) {
        return array_merge_recursive(...$data);
    });

    $promises[] = $lines;
}

//    $promises[] = $addrLoader->load($o['id'])
//        ->then(function ($lines) use ($o){
//            $o['addr'] = $lines;
//            return $o;
//        });

$data = DataLoader::await($promiseAdapter->createAll($promises));
print_r($data);