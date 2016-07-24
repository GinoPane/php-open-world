<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\SourceLoaderResults\JsonResult;
use OpenWorld\Data\SourceLoaderResults\Factories\JsonResultFactory;

class JsonResultTest extends TestCase
{
    public function testJsonResultCreateFromFactory()
    {
        $result = JsonResultFactory::get();

        $this->assertInstanceOf(JsonResult::class, $result);
    }
}
