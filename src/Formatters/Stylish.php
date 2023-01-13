<?php

namespace src\Formatters\Stylish;

use function Funct\Collection\flattenAll;

function makeIndent(int $depth): string
{
    return str_repeat(" ", 4 * $depth);
}

function prepareValue($value, int $depth): string
{
    if ($value == '') {
        return ' ';
    }
    if (is_bool($value)) {
        return $value = true ? 'true' : 'false';
    }
    if ($value == null) {
        return 'null';
    }
    if (!is_array($value)) {
        return $value;
    } else {
        $indent = makeIndent($depth);

        $lines = array_map(function ($key) use ($value, $indent, $depth) {
            $stringValue = prepareValue($value[$key], $depth + 1);
            return "{$indent}    {$key}: {$stringValue}";
        }, array_keys($value));
        $preparedValue = implode("\n", $lines);
        return "{\n{$preparedValue}\n{$indent}}";
    }
}

function makeStylish(array $diff): string
{
    $iter = function (array $diff, int $depth) use (&$iter): array {
        return array_map(function ($node) use ($depth, $iter) {
            [
                'key' => $key,
                'type' => $type,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
                'children' => $children
            ] = $node;

            $indent = makeIndent($depth - 1);

            switch ($type) {
                case 'complex':
                    $indentAfter = makeIndent($depth);
                    return ["{$indent}    {$key}: {", $iter($children, $depth + 1), "{$indentAfter}}"];
                case 'added':
                    $preparedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}  + {$key}: {$preparedNewValue}";
                case 'removed':
                    $preparedOldValue = prepareValue($oldValue, $depth);
                    return "{$indent}  - {$key}: {$preparedOldValue}";
                case 'unchanged':
                    $preparedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}    {$key}: {$preparedNewValue}";
                case 'updated':
                    $preparedOldValue = prepareValue($oldValue, $depth);
                    $preparedNewValue = prepareValue($newValue, $depth);
                    $addedLine = "{$indent}  + {$key}: {$preparedNewValue}";
                    $deletedLine = "{$indent}  - {$key}: {$preparedOldValue}";
                    return implode("\n", [$deletedLine, $addedLine]);
            };
        }, $diff);
    };
    return implode("\n", flattenAll(['{', $iter($diff, 1), '}']));
}
