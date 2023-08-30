<?php

function unsetSimple(array $array, string $unset): array
{
    unset($array[$unset]);
    return $array;
}

function unsetArrayFilter(array $array, string $unset): array
{
    return array_filter($array, fn($key) => $key !== $unset, ARRAY_FILTER_USE_KEY);
}

function unsetArrayDiffKey(array $array, string $unset): array
{
    return array_diff_key($array, [$unset => null]);
}


$test = [
    'a' => '1',
    'b' => '2',
    'c' => '3',
    'd' => '4',
    'e' => '5',
    'f' => '6',
];

$functions = ['unsetSimple', 'unsetArrayFilter', 'unsetArrayDiffKey'];
foreach ($functions as $function) {
    $s = microtime(true);
    for ($i = 0; $i < 100000; $i++) {
        $function($test, 'd');
    }
    echo "$function time: " . (microtime(true) - $s) . PHP_EOL;
}