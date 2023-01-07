<?php

namespace tests\Test;

use PHPUnit\Framework\TestCase;

use function src\Differ\genDiff;

class Test extends TestCase
{
    private $pathJson1;
    private $pathJson2;
    private $pathYaml1;
    private $pathYaml2;
    private $pathJsonResult;

    public function setUp(): void
    {
        $this->pathJson1 = 'tests/fixtures/file1.json';
        $this->pathJson2 = 'tests/fixtures/file2.json';
        $this->pathYaml1 = 'tests/fixtures/file1.yml';
        $this->pathYaml2 = 'tests/fixtures/file2.yml';
    }

    public function testGenDifJsonStylish(): void
    {
        $path = 'tests/fixtures/result.txt';
        $expected = file_get_contents($path);
        $result = genDiff($this->pathJson1, $this->pathJson2);
        $this->assertTrue($expected == $result);
    }

    public function testGenDiffYamlStylish(): void
    {
        $path = 'tests/fixtures/result.txt';
        $expected = file_get_contents($path);
        $result = genDiff($this->pathYaml1, $this->pathYaml2);
        $this->assertTrue($expected == $result);
    }
}
