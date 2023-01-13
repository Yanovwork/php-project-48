<?php

namespace src\Merger;

use function Functional\sort;
use function Funct\Collection\union;

function makeNode(string $key, string $type, $oldValue, $newValue, $children = null): array
{
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}

function makeDiff(array $arr1, array $arr2): array
{
    $keys1 = array_keys($arr1);
    $keys2 = array_keys($arr2);
    $unionKeys = union($keys1, $keys2);
    $sortedKeys = sort($unionKeys, fn ($key1, $key2) => $key1 <=> $key2);

    $callback = function ($key) use ($arr1, $arr2) {
        $value1 = $arr1[$key] ?? null;
        $value2 = $arr2[$key] ?? null;
        if (!array_key_exists($key, $arr1)) {
            return makeNode($key, 'added', null, $value2);
        }
        if (!array_key_exists($key, $arr2)) {
            return makeNode($key, 'removed', $value1, null);
        }
        if (is_array($value1) && is_array($value2)) {
            return makeNode($key, 'complex', null, null, makeDiff($value1, $va));
        }
        if ($value1 == $value2) {
            return makeNode($key, 'unchanged', $value1, $value2);
        }
        return makeNode($key, 'updated', $value1, $value2);
    };
    return array_map($callback, $sortedKeys);
}
