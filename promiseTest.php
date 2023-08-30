<?php
require 'vendor/autoload.php';

$resolver = function (callable $resolve, callable $reject, callable $notify) {


    //$resolve($awesomeResult);
    // or throw new Exception('Promise rejected');
    // or $resolve($anotherPromise);
    // or $reject($nastyError);
    // or $notify($progressNotification);
};

$initialData = [
    ['name' => 'Order1', 'id' => 1],
    ['name' => 'Order2', 'id' => 2],
];

$deferred = new React\Promise\Deferred();

$deferred->promise()->then(function (array $data) {
    $promises = [];
    foreach ($data as $d) {
        $def = new \React\Promise\Deferred();
        $promises[] = $def->promise();
    }
    return $a
});