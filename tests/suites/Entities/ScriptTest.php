<?php

use OpenWorld\Entities\Script;
use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;
use OpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;

/**
 * Class ScriptTest
 */
class ScriptTest extends OpenWorldTestCase
{
    /**
     * @test
     */
    public function it_throws_exception_on_invalid_key_source()
    {
        $this->expectException(FileNotFoundException::class);

        $script = $this
            ->getMockBuilder(Script::class)
            ->setMethodsExcept(['getAssertedCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $keySourceUri = $this->getInternalProperty($script, 'keySourceUri');
        $this->setInternalProperty($script, 'keySourceUri', 'foo');
        $assertKey = $this->getInternalMethod($script, 'getAssertedCode');

        try {
            $assertKey->invoke($script, '');
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->setInternalProperty($script, 'keySourceUri', $keySourceUri);
        }
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_script()
    {
        $this->expectException(InvalidKeyPropertyValueException::class);

        $script = new Script('Foo');

        $this->assertNotNull($script);
    }

    /**
     * @test
     *
     * @param $scriptCode
     *
     * @dataProvider getValidScriptCodes
     */
    public function it_creates_script_for_valid_identifier($scriptCode)
    {
        $script = new Script($scriptCode);

        $this->assertInstanceOf(Script::class, $script);
        $this->assertEquals(strtolower($scriptCode), strtolower($script->getCode()));
    }

    /**
     * @test
     *
     * @param $scriptCode
     * @param $expectedResult
     *
     * @dataProvider getVariousScriptCodes
     */
    public function it_validates_script_code($scriptCode, $expectedResult)
    {
        $this->assertEquals(Script::codeIsLikelyValid($scriptCode), $expectedResult);
    }

    /**
     * @test
     *
     * @param $scriptCode
     *
     * @dataProvider getValidScriptCodes
     */
    public function it_asserts_script_with_predicate($scriptCode)
    {
        $script = $this
            ->getMockBuilder(Script::class)
            ->setMethodsExcept(['getAssertedCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($script, 'keySourceUri', 'script.codes.json');

        $assertKey = $this->getInternalMethod($script, 'getAssertedCode');

        $validKey = $assertKey->invoke($script, $scriptCode, function ($key, $source) {
            $keyIndex = array_search(strtolower($key), array_map('strtolower', $source));

            return $keyIndex !== false ? $source[$keyIndex] : $keyIndex;
        });

        $this->assertEquals(strtolower($scriptCode), strtolower($validKey));
    }

    /**
     * @test
     */
    public function it_throws_exception_for_failure_predicate()
    {
        $this->expectException(InvalidKeyPropertyValueException::class);

        $script = $this
            ->getMockBuilder(Script::class)
            ->setMethodsExcept(['getAssertedCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($script, 'keySourceUri', 'script.codes.json');

        $assertKey = $this->getInternalMethod($script, 'getAssertedCode');

        $validKey = $assertKey->invoke($script, 'Cyrl', function () {
            return false;
        });

        $this->assertNull($validKey);
    }

    /**
     * @param $scriptCode
     * @param $expectedCode
     *
     * @test
     * @dataProvider getValidScriptAliasCodes
     */
    public function it_checks_alias_substitution($scriptCode, $expectedCode)
    {
        $script = new Script($scriptCode);

        $this->assertInstanceOf(Script::class, $script);
        $this->assertEquals(strtolower($expectedCode), strtolower($script->getCode()));
    }

    /**
     * Provides valid script codes data
     *
     * @return array
     */
    public function getValidScriptCodes()
    {
        return [
            ['Cyrl'],
            ['Latn'],
            ['Takr'],
            ['cyrl'],
            ['lAtN'],
        ];
    }

    /**
     * Provides script code aliases
     *
     * @return array
     */
    public function getValidScriptAliasCodes()
    {
        return [
            ['Qaai', 'Zinh']
        ];
    }

    /**
     * Provides script codes for validation
     *
     * @return array
     */
    public function getVariousScriptCodes()
    {
        return [
            ['Cyrl', true],
            ['123', false],
            ['Latn', true],
            ['FooBar', false],
            ['FooBar', false],
            ['Tale', true]
        ];
    }
}
