<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\SourceLoaders\FileSourceLoader;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Exceptions\InvalidContentException;

class JsonResultSourceLoaderTest extends TestCase
{
    /**
     * @var FileSourceLoader
     */
    public $loader = null;

    public function setUp()
    {
        $this->loader = new FileSourceLoader(new JsonResultFactory());
    }

    /**
     * @param string $file
     *
     * @dataProvider validDataProvider
     */
    public function testValidJsonFiles(string $file)
    {
        $content = $this->loader->load($file)->getContent();

        $this->assertStringEqualsFile($file ,$content);
    }

    /**
     * @param string $file
     *
     * @dataProvider invalidDataProvider
     */
    public function testInvalidJsonFiles(string $file)
    {
        $this->expectException(InvalidContentException::class);

        $loadResult = $this->loader->load($file);
    }

    /**
     * Based on the list of json test suite.
     *
     * @link http://www.json.org/JSON_checker/
     */
    public function validDataProvider()
    {
        $directory = new DirectoryIterator(__DIR__.'/../../../data/json/valid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }

    /**
     * Based on the list of json test suite.
     *
     * @link http://www.json.org/JSON_checker/
     */
    public function invalidDataProvider()
    {
        $directory = new DirectoryIterator(__DIR__.'/../../../data/json/invalid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }
}
