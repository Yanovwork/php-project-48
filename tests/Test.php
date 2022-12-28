<?php

namespace tests\Test;

use PHPUnit\Framework\TestCase;

use function src\Differ\genDiff;

class Test extends TestCase
{
    private $pathJson1;
    private $pathJson2;

    public function setUp(): void
    {
        $this->pathJson1 = 'tests/fixtures/file1.json';
        $this->pathJson2 = 'tests/fixtures/file2.json';
    }

    public function testGenDiff(): void
    {
        $path = 'tests/fixtures/result.txt';
        $expected = file_get_contents($path);
        $resultJson = genDiff($this->pathJson1, $this->pathJson2);
        $this->assertTrue($expected == $resultJson);
    }
}
