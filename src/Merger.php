<?php

namespace src\Merger;

use function Functional\sort;

function makeLeaf(string $key, string $type, $value1, $value2 = null): array
{
    return ['key' => $key, 'type' => $type, 'value1' => $value1, 'value2' => $value2];
}

function makeNode(string $key, string $type, array $children): array
{
    return ['key' => $key, 'type' => $type, 'children' => $children];
}

function getKey($diff)
{
    return $diff['key'];
}

function getValue1($diff)
{
    return $diff['value1'];
}

function getValue2($diff)
{
    return $diff['value2'];
}

function getType($diff): string
{
    return $diff['type'];
}

function getChildren($root)
{
    return $root['children'];
}

function makeTree(array $content1, array $content2): array
{
    $keys1 = array_keys($content1);
    $keys2 = array_keys($content2);
    $keys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));

    $callback = function ($key) use ($content1, $content2) {
        $value1 = $content1[$key] ?? null;
        $value2 = $content2[$key] ?? null;

        if (!array_key_exists($key, $content1)) {
            return makeLeaf($key, 'added', $value2);
        }

        if (!array_key_exists($key, $content2)) {
            return makeLeaf($key, 'removed', $value1);
        }

        if ($value1 === $value2) {
            return makeLeaf($key, 'unchanged', $value1);
        }

        if (!is_array($value1) || !is_array($value2)) {
            return makeLeaf($key, 'updated', $value1, $value2);
        }

        $result = makeTree($value1, $value2);

        return makeNode($key, 'nested', $result);
    };

    return array_map($callback, $sortedKeys);
}

function makeDiff($content1, $content2)
{
    $children = makeTree($content1, $content2);
    return [
        'type' => 'root',
        'children' => $children
    ];
}
