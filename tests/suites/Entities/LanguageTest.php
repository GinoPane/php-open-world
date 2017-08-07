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
     *
     * @dataProvider getValidLanguageCodes
     */
    public function it_creates_language_for_valid_identifier($languageCode, $expectedCode)
    {
        $script = new Language($languageCode);

        $this->assertInstanceOf(Language::class, $script);
        $this->assertEquals(strtolower($expectedCode), strtolower($script->getCode()));
    }

    /**
     * Provides valid language codes data
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
}