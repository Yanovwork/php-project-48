<?php

namespace src\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $filePath): array
{
    $result = [];
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'json') {
        $result = parseJson($filePath);
    } else {
        $result = Yaml::parseFile($filePath);
    }
    return $result;
}

function parseJson(string $filePath): array
{
    return json_decode(file_get_contents($filePath), true);
}
