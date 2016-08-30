<?php


use PHPUnit\Framework\TestCase;

use OpenWorld\Data\SourceLoaderResults\JsonResult;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;

use OpenWorld\Exceptions\InvalidContentException;

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
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_all_valid_json_examples
     */
    public function it_tests_if_content_can_be_accessed_as_string(string $json)
    {
        $this->resultClass->setContent($json);

        $this->assertEquals($this->resultClass->asString(), $json);
    }

    /**
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_all_valid_json_examples
     */
    public function it_tests_that_raw_content_is_the_same_as_string(string $json)
    {
        $this->resultClass->setContent($json);

        $this->assertEquals($this->resultClass->asString(), $this->resultClass->getContent());
    }

    /**
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_valid_json_as_array
     */
    public function it_tests_if_content_can_be_accessed_as_array(string $json)
    {
        $this->resultClass->setContent($json);

        $this->assertTrue(is_array($this->resultClass->asArray()));
    }

    /**
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_valid_json_as_object
     */
    public function it_tests_if_content_can_be_accessed_as_object(string $json)
    {
        $this->resultClass->setContent($json);

        $this->assertTrue(is_object($this->resultClass->asObject()));
    }

    /**
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_invalid_json_as_array
     */
    public function it_throws_an_exception_when_invalid_content_for_array(string $json)
    {
        $this->expectException(InvalidContentException::class);

        $this->resultClass->setContent($json);

        $this->assertTrue(is_array($this->resultClass->asArray()));
    }

    /**
     * @param string $json
     *
     * @test
     *
     * @dataProvider provides_access_to_invalid_json_as_object
     */
    public function it_throws_an_exception_when_invalid_content_for_object(string $json)
    {
        $this->expectException(InvalidContentException::class);

        $this->resultClass->setContent($json);

        $this->assertTrue(is_array($this->resultClass->asObject()));
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

    /**
     * @return array
     */
    public function provides_access_to_all_valid_json_examples()
    {
        return array_merge(
            $this->provides_access_to_valid_json_as_string(),
            $this->provides_access_to_valid_json_as_array(),
            $this->provides_access_to_valid_json_as_object()
        );
    }

    /**
     * @return array
     */
    public function provides_access_to_valid_json_as_string()
    {
        return array_merge([
            ["\"foo bar\""],
        ], $this->provides_access_to_valid_json_as_reserved_words());
    }

    /**
     * @return array
     */
    public function provides_access_to_valid_json_as_array()
    {
        return [
            ["[0, 1, 2]"],
            ["[\"foo\"]"],
            ["{\"foo\" : \"bar\"}"],
        ];
    }

    /**
     * @return array
     */
    public function provides_access_to_valid_json_as_object()
    {
        return [
            ["{\"foo\" : \"bar\"}"],
            ["{\"foo\" : {\"bar\" : [0, 1, 2]}}"],
        ];
    }

    /**
     * @return array
     */
    public function provides_access_to_invalid_json_as_array()
    {
        return array_merge([
            ["\"foo\""],
        ], $this->provides_access_to_valid_json_as_reserved_words());
    }

    /**
     * @return array
     */
    public function provides_access_to_invalid_json_as_object()
    {
        return array_merge([
            ["[0, 1, 2]"],
            ["[\"foo\"]"],
        ], $this->provides_access_to_valid_json_as_reserved_words());
    }

    /**
     * @return array
     */
    public function provides_access_to_valid_json_as_reserved_words()
    {
        return [
            ["true"],
            ["false"],
            ["null"]
        ];
    }
}
