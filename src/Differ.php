<?php

namespace Differ\Differ;

use function src\Parsers\parseFile;
use function src\Merger\makeDiff;
use function src\Formatters\Stylish\makeStylish;
use function src\Formatters\Plain\makePlain;
use function src\Formatters\Json\makeJson;

function genDiff($firstPath, $secondPath, string $format = 'stylish'): string
{
    $firstArray = parseFile($firstPath);
    $secondArray = parseFile($secondPath);
    $result = makeDiff($firstArray, $secondArray);
    switch ($format) {
        case 'stylish':
            return makeStylish($result);
        case 'plain':
            return makePlain($result);
        case 'json':
            return makeJson($result);
    }
}
