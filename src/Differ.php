<?php

namespace src\Differ;

use function Functional\sort;

function parseFile(string $filePath): array
{
    return json_decode(file_get_contents($filePath), true);
}

function convertBooleanToString(array $arr): array
{
    $result = [];
    foreach ($arr as $key => $value) {
        if (is_bool($key)) {
            $key == true ? $key = 'true' : $key = 'false';
        }
        if (is_bool($value)) {
            $value == true ? $value = 'true' : $value = 'false';
        }
        $result[$key] = $value;
    }
    return $result;
}

function makingUnitedArray(array $arr1, array $arr2): array
{
    $sumOfArrays = [];
    foreach ($arr1 as $key => $value) {
        if (array_key_exists($key, $arr2) && $arr1[$key] == $arr2[$key]) {
            $sumOfArrays[] = "    {$key}: {$arr1[$key]}";
        } elseif (!array_key_exists($key, $arr2)) {
            $sumOfArrays[] = "  - {$key}: {$arr1[$key]}";
        } elseif (array_key_exists($key, $arr2) && $arr1[$key] !== $arr2[$key]) {
            $sumOfArrays[] = "  - {$key}: {$arr1[$key]}";
        }
    }
    foreach ($arr2 as $key => $value) {
        if (!array_key_exists($key, $arr1)) {
            $sumOfArrays[] = "  + {$key}: {$arr2[$key]}";
        } elseif (array_key_exists($key, $arr1) && $arr2[$key] !== $arr1[$key]) {
            $sumOfArrays[] = "  + {$key}: {$arr2[$key]}";
        }
    }
    return $sumOfArrays;
}

function genDiff($firstPath, $secondPath): string
{
    $firstArray = convertBooleanToString(parseFile($firstPath));
    $secondArray = convertBooleanToString(parseFile($secondPath));
    $unitedArray = makingUnitedArray($firstArray, $secondArray);
    $result = sort($unitedArray, fn ($a, $b) => substr($a, 3, -3) <=> substr($b, 3, -3), false);
    return "{\n" . implode("\n", $result) . "\n}";
}
