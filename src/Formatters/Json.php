<?php

namespace src\Formatters\Json;

function makeJson(array $diff): string
{
    $encoded = json_encode($diff, JSON_PRETTY_PRINT);
    return $encoded;
}
