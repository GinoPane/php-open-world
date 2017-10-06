<?php

use OpenWorld\Entities\Language;

/**
 * Class LanguageTest
 */
class LanguageTest extends OpenWorldTestCase
{
    /**
     * @test
     * @param $languageCode
     * @param $expectedCode
     *
     * @dataProvider getValidLanguageCodes
     */
    public function it_creates_language_for_valid_identifier($languageCode, $expectedCode)
    {
        $language = new Language($languageCode);

        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals(strtolower($expectedCode), strtolower($language->getCode()));
    }

    /**
     * @test
     *
     * @param $languageCode
     * @param $expectedResult
     *
     * @dataProvider getVariousLanguageCodes
     */
    public function it_validates_language_code($languageCode, $expectedResult)
    {
        $this->assertEquals(Language::codeIsLikelyValid($languageCode), $expectedResult);
    }

    /**
     * Provides valid language codes data
     *
     * @return array
     */
    public function getValidLanguageCodes()
    {
        return [
            ['RU', 'ru'],
            ['be', 'be'],
            ['eN', 'en'],
            ['zu', 'zu'],
            ['iT', 'it'],
            ['rus', 'ru'],
            ['bel', 'be'],
            ['rum', 'ro'],
            ['wel', 'cy']
        ];
    }

    /**
     * Provides various language codes data for validation
     *
     * @return array
     */
    public function getVariousLanguageCodes()
    {
        return [
            ['RU', true],
            ['be', true],
            ['eN', true],
            ['zu', true],
            ['rus', true],
            ['120', false],
            ['a', false],
            ['english', false]
        ];
    }
}
