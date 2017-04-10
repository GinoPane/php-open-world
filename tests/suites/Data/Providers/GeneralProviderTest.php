<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;

class GeneralProviderTest extends TestCase
{
    /**
     * @var GeneralProvider
     */
    public $provider = null;

    public function setUp()
    {
        $this->provider = new GeneralProvider(
            new FileSourceLoader(),
            new JsonResultFactory()
        );
    }

    /**
     * @test
     */
    public function it_checks_get_result_factory_class()
    {
        $this->assertInstanceOf(SourceLoaderResultFactoryInterface::class, $this->provider->getResultFactory());
    }

    /**
     * @test
     */
    public function it_checks_get_loader_return_type()
    {
        $this->assertInstanceOf(SourceLoaderInterface::class, $this->provider->getLoader());
    }
}
