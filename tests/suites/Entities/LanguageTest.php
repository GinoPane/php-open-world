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
    public function it_creates_language_for_valid_identifier($languageCode)
    {
        $script = new Language($languageCode);

        $this->assertInstanceOf(Language::class, $script);
        $this->assertEquals(strtolower($languageCode), strtolower($script->getCode()));
    }

    /**
     * Provides valid language codes data
     */
    public function getValidLanguageCodes()
    {
        return [
            ['RU'],
            ['be'],
            ['eN'],
            ['zu'],
            ['iT']
        ];
    }
}
