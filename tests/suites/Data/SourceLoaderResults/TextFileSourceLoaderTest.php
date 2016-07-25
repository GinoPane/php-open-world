<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\SourceLoaders\TextFileSourceLoader;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;

class FileSourceLoaderTest extends TestCase
{
    /**
     * @var TextFileSourceLoader
     */
    public $loader = null;

    public function setUp()
    {
        $this->loader = new TextFileSourceLoader(new JsonResultFactory());
    }

    public function testFileLoaderResultCreate()
    {
        $this->assertInstanceOf(TextFileSourceLoader::class, $this->loader);
    }

    public function testGetFactory()
    {
        $this->assertInstanceOf(SourceLoaderResultFactoryInterface::class, $this->loader->getResultFactory());
    }
}
