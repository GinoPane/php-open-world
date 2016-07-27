<?php

namespace OpenWorld\Data\SourceLoaders;


use PHPUnit\Framework\TestCase;

use DirectoryIterator;

use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Exceptions\FileNotValidException;

$overrideIsFile = false;

function is_file($path) {
    global $overrideIsFile;

    if ($overrideIsFile) {
        return true;
    } else {
        return \is_file($path);
    }
}

class FileSourceLoaderTest extends TestCase
{
    /**
     * @var FileSourceLoader
     */
    public $loader = null;

    public function setUp()
    {
        $this->loader = new FileSourceLoader();
    }

    public function testFileLoaderResultCreate()
    {
        $this->assertInstanceOf(FileSourceLoader::class, $this->loader);
    }

    /**
     * @dataProvider filesProvider
     */
    public function testLoad(string $path)
    {
        $this->assertStringEqualsFile($path, $this->loader->loadSource($path));
    }

    public function testNotReadableOrNotExistent()
    {
        $this->expectException(FileNotFoundException::class);

        $this->loader->loadSource('non-existent-path');
    }

    public function testNotFile()
    {
        $this->expectException(FileNotValidException::class);

        $this->loader->loadSource(__DIR__);
    }

    public function testInvalidContentsFile()
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
    public function filesProvider()
    {
        $directory = new DirectoryIterator(__DIR__ . '/../../../data/json/valid/');

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getPathname()];
            }
        }
    }
}
