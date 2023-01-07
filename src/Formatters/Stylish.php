<?php

namespace src\Formatters\Stylish;

use function src\Merger\getKey;
use function src\Merger\getValue1;
use function src\Merger\getValue2;
use function src\Merger\getType;
use function src\Merger\getChildren;

function getIndent(int $depth): string
{
    return str_repeat('    ', $depth - 1);
}

function wrapLines(array $lines, string $indent): string
{
    return " {\n" . implode("\n", $lines) . "\n" . $indent . "}";
}

function toString($item, int $depth): string
{
    if ($item === true) {
        return ' true';
    }
    if ($item === false) {
        return ' false';
    }
    if ($item === null) {
        return ' null';
    }
    if ($item === 0) {
        return ' 0';
    }
    if ($item === '') {
        return '';
    }
    if (!is_array($item)) {
        return " $item";
    }

    $indent = getIndent($depth);

    $lines = array_map(function ($key, $value) use ($indent, $depth) {
        $stringValue = toString($value, $depth + 1);
        return "{$indent}    {$key}:{$stringValue}";
    }, array_keys($item), $item);

    return wrapLines($lines, $indent);
}

function makeFormat($currentValue, int $depth): string
{
    $indent = getIndent($depth);

    $callback = function ($acc, $item) use ($indent, $depth) {
        $key = getKey($item);
        $type = getType($item);

        if ($type === 'nested') {
            $children = makeFormat(getChildren($item), $depth + 1);
            return [...$acc, "{$indent}    {$key}:{$children}"];
        }

        $value1 = toString(getValue1($item), $depth + 1);
        $value2 = toString(getValue2($item), $depth + 1);

        if ($type === 'added') {
            return [...$acc, "{$indent}  + {$key}:{$value1}"];
        }

        if ($type === 'removed') {
            return [...$acc, "{$indent}  - {$key}:{$value1}"];
        }

        if ($type === 'updated') {
            return [
                ...$acc,
                "{$indent}  - {$key}:{$value1}",
                "{$indent}  + {$key}:{$value2}"
            ];
        }

        return [...$acc, "{$indent}    {$key}:{$value1}"];
    };
    $lines = array_reduce($currentValue, $callback, []);

    return wrapLines($lines, $indent);
}

function makeStylish(array $diff): string
{
    return makeFormat(getChildren($diff), 1);
}
