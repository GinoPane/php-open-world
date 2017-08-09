<?php

use OpenWorld\Entities\Locale;
use OpenWorld\Entities\Script;
use OpenWorld\Entities\Language;
use OpenWorld\Entities\Territory;


/**
 * Class LocaleTest
 */
class LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getDataForLocales
     *
     * @param $languageCode
     * @param $scriptCode
     * @param $territoryCode
     * @param $expectedLocaleCode
     */
    public function it_creates_locale_for_valid_identifier($languageCode, $scriptCode, $territoryCode, $expectedLocaleCode)
    {
        $script = null;
        $territory = null;

        if ($scriptCode) {
            $script = new Script($scriptCode);
        }

        if ($territoryCode) {
            $territory = new Territory($territoryCode);
        }

        $locale = new Locale(new Language($languageCode), $script, $territory);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals($expectedLocaleCode, $locale->getCode());
    }

    /**
     * @test
     * @dataProvider getDataForLocalesFromString
     *
     * @param $localeString
     * @param $expectedLocaleCode
     */
    public function it_creates_locale_from_string($localeString, $expectedLocaleCode)
    {
        $locale = Locale::fromString($localeString);

        $this->assertEquals($expectedLocaleCode, $locale->getCode());
    }

    /**
     * @return array
     */
    public function getDataForLocales()
    {
        return [
            ['ru', '', '', 'ru_Cyrl_RU'],
            ['rus', 'Cyrl', 'BEL', 'ru_Cyrl_BE'],
            ['man', '', '', 'man_Latn_GM'],
            ['man', '', 'GN', 'man_Nkoo_GN'],
            ['man', 'Nkoo', '', 'man_Nkoo_GN'],
            ['ff', '', '', 'ff_Latn_SN'],
            ['sr', '', 'ME', 'sr_Latn_ME']
        ];
    }

    /**
     * @return array
     */
    public function getDataForLocalesFromString()
    {
        return [
            ['ru', 'ru_Cyrl_RU'],
            ['rus_Cyrl_BEL', 'ru_Cyrl_BE'],
            ['und_NC', 'fr_Latn_NC'],
            ['und_Hani', 'zh_Hani_CN'],
            ['und_Latn_TN', 'fr_Latn_TN'],
            ['es_419', 'es_Latn_419']
        ];
    }
}
