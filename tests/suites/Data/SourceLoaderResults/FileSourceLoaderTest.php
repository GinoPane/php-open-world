<?php

namespace OpenWorld\Data\SourceLoaders;

use PHPUnit\Framework\TestCase;

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
        $this->loader = new FileSourceLoader(new JsonResultFactory());
    }

    public function testFileLoaderResultCreate()
    {
        $this->assertInstanceOf(FileSourceLoader::class, $this->loader);
    }

    public function testGetFactory()
    {
        $this->assertInstanceOf(SourceLoaderResultFactoryInterface::class, $this->loader->getResultFactory());
    }

    public function testNotReadableOrNotExistent()
    {
        $this->expectException(FileNotFoundException::class);

        $this->loader->load('non-existent-path');
    }

    public function testNotFile()
    {
        $this->expectException(FileNotValidException::class);

        $this->loader->load(__DIR__);
    }

    public function testInvalidContentsFile()
    {
        //use monkey-patching here to override is_file
        global $overrideIsFile;

        $overrideIsFile = true;

        $this->expectException(FileNotValidException::class);

        $this->loader->load(__DIR__);
    }
}
