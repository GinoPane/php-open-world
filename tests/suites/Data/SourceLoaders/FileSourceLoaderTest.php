<?php

namespace OpenWorld\Data\SourceLoaders;

use PHPUnit\Framework\TestCase;

use DirectoryIterator;

use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Exceptions\FileNotValidException;

/**
 * Override system function or not
 */
$overrideIsFile = false;

/**
 * Mock system function for testing purpose
 *
 * @param $path
 * @return bool
 */
function is_file($path) {
    global $overrideIsFile;

    if ($overrideIsFile) {
        return true;
    } else {
        return \is_file($path);
    }
}

/**
 * Class FileSourceLoaderTest
 * @package OpenWorld\Data\SourceLoaders
 */
class FileSourceLoaderTest extends TestCase
{
    /**
     * @var FileSourceLoader
     */
    public $loader = null;

    /**
     *
     */
    public function setUp()
    {
        $this->loader = new FileSourceLoader();
    }

    /**
     * @test
     */
    public function it_checks_type_of_created_loader()
    {
        $this->assertInstanceOf(FileSourceLoader::class, $this->loader);
    }

    /**
     * @test
     *
     * @dataProvider provides_access_to_valid_json_files
     *
     * @param string $path
     */
    public function it_loads_files_using_source_loader(string $path)
    {
        $this->assertStringEqualsFile($path, $this->loader->loadSource($path));
    }

    /**
     * @test
     */
    public function it_throws_exceptions_for_non_existent_path()
    {
        $this->expectException(FileNotFoundException::class);

        $this->loader->loadSource('non-existent-path');
    }

    /**
     * @test
     */
    public function it_throws_exceptions_for_not_file()
    {
        $this->expectException(FileNotValidException::class);

        $this->loader->loadSource(__DIR__);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_content()
    {
        //use monkey-patching here to override is_file()
        global $overrideIsFile;
        $overrideIsFile = true;

        $this->expectException(FileNotValidException::class);

        $this->loader->loadSource(__DIR__);
    }

    /**
     * Simple files to test FileSourceLoader::load()
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
}
