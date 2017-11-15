<?php

//for monkey-patching
namespace GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaders;

use PHPUnit\Framework\TestCase;

use DirectoryIterator;

use GinoPane\PhpOpenWorld\Exceptions\FileNotFoundException;
use GinoPane\PhpOpenWorld\Exceptions\FileNotValidException;
use GinoPane\PhpOpenWorld\Exceptions\BadDataFileContentsException;

/**
 * Override system function is_file or not
 */
$overrideIsFile = false;

/**
 * Override system function is_dir or not
 */
$overrideIsDir = false;

/**
 * Override system function is_readable or not
 */
$overrideIsReadable = false;

/**
 * Mock the system function for testing purpose
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
 * Mock the system function for testing purpose
 *
 * @param $path
 * @return bool
 */
function is_dir($path) {
    global $overrideIsDir;

    if ($overrideIsDir) {
        return false;
    } else {
        return \is_dir($path);
    }
}

/**
 * Mock the system function for testing purpose
 *
 * @param $path
 * @return bool
 */
function is_readable($path) {
    global $overrideIsReadable;

    if ($overrideIsReadable) {
        return true;
    } else {
        return \is_readable($path);
    }
}

/**
 * Class FileSourceLoaderTest
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

    public function tearDown()
    {
        //disable monkey-patching here to override is_file()
        global $overrideIsFile, $overrideIsDir, $overrideIsReadable;
        $overrideIsFile = false;
        $overrideIsDir = false;
        $overrideIsReadable = false;
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
        global $overrideIsFile, $overrideIsDir, $overrideIsReadable;
        $overrideIsFile = true;
        $overrideIsDir = true;
        $overrideIsReadable = true;

        $this->expectException(BadDataFileContentsException::class);

        $this->loader->loadSource('non-existent-path');
    }

    /**
     * Simple files to test FileSourceLoader::load()
     */
    public function provides_access_to_valid_json_files()
    {
        $directory = new DirectoryIterator(PROJECT_ROOT . '/tests/data/json/valid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }
}
