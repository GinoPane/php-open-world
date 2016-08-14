<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\SourceLoaderResults\JsonResult;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;

class JsonResultTest extends TestCase
{
    /**
     * @var JsonResult
     */
    private $resultClass = null;

    public function setUp()
    {
        $this->resultClass = JsonResultFactory::get();
    }

    public function testJsonResultCreateFromFactory()
    {
        $result = JsonResultFactory::get();

        $this->assertInstanceOf(JsonResult::class, $result);
    }

    /**
     * @param string $file
     *
     * @dataProvider validDataProvider
     */
    public function testValidJsonFiles(string $file)
    {
        $this->assertTrue($this->resultClass->isValid(file_get_contents($file)));
    }

    /**
     * @param string $file
     *
     * @dataProvider invalidDataProvider
     */
    public function testInvalidJsonFiles(string $file)
    {
        $this->assertFalse($this->resultClass->isValid(file_get_contents($file)));
    }

    /**
     * Based on the file list from json test suite.
     *
     * @link http://www.json.org/JSON_checker/
     */
    public function validDataProvider()
    {
        $directory = new DirectoryIterator(__DIR__ . '/../../../data/json/valid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }

    /**
     * Based on the file list from json test suite.
     *
     * @link http://www.json.org/JSON_checker/
     */
    public function invalidDataProvider()
    {
        $directory = new DirectoryIterator(__DIR__ . '/../../../data/json/invalid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }
}
