<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\Providers\GeneralProvider;
use OpenWorld\Data\SourceLoaders\FileSourceLoader;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;
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
}
