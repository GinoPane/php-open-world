<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\SourceLoaderResults\JsonResult;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;

/**
 * Class JsonResultTest
 */
class JsonResultTest extends TestCase
{
    /**
     * @var JsonResult
     */
    private $resultClass = null;

    /**
     *
     */
    public function setUp()
    {
        $this->resultClass = JsonResultFactory::get();
    }

    /**
     * @test
     */
    public function it_creates_json_result_from_factory()
    {
        $result = JsonResultFactory::get();

        $this->assertInstanceOf(JsonResult::class, $result);
    }

    /**
     * @param string $file
     *
     * @test
     * @dataProvider provides_access_to_valid_json_files
     */
    public function it_tests_is_valid_method_against_valid_json_files(string $file)
    {
        $this->assertTrue($this->resultClass->isValid(file_get_contents($file)));
    }

    /**
     * @test
     * @dataProvider provides_access_to_invalid_json_files
     *
     * @param string $file
     */
    public function it_tests_is_valid_method_against_invalid_json_files(string $file)
    {
        $this->assertFalse($this->resultClass->isValid(file_get_contents($file)));
    }

    /**
     * Based on the file list from json test suite.
     *
     * @link http://www.json.org/JSON_checker/
     */
    public function provides_access_to_valid_json_files()
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
    public function provides_access_to_invalid_json_files()
    {
        $directory = new DirectoryIterator(__DIR__ . '/../../../data/json/invalid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }
}
