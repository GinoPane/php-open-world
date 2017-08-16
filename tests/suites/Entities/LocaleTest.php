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
     * @test
     * @dataProvider getDataForLocaleParentCodes
     *
     * @param $localeString
     * @param $expectedParentCode
     */
    public function it_gets_locale_parent_code(string $localeString, $expectedParentCode)
    {
        $locale = Locale::fromString($localeString);

        $this->assertEquals($expectedParentCode, $locale->getParentCode());
    }

    /**
     * @test
     * @dataProvider getDataForLocaleAlternatives
     *
     * @param string $localeString
     * @param array $expectedAlternatives
     */
    public function it_gets_locale_alternatives(string $localeString, array $expectedAlternatives)
    {
        $locale = Locale::fromString($localeString);

        $this->assertEquals($expectedAlternatives, $locale->getAlternativeCodes());
    }

    /**
     * @return array
     */
    public function getDataForLocales()
    {
        return [
            ['ru', '', '', 'ru_Cyrl_RU'],
            ['rus', 'Cyrl', 'BLR', 'ru_Cyrl_BY'],
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
            ['rus_Cyrl_BLR', 'ru_Cyrl_BY'],
            ['und_NC', 'fr_Latn_NC'],
            ['und_Hani', 'zh_Hani_CN'],
            ['und_Latn_TN', 'fr_Latn_TN'],
            ['es_419', 'es_Latn_419'],
            ['aa_SAAHO', 'ssy_Latn_ER'],
            ['en-US-POSIX', '']
        ];
    }

    /**
     * @return array
     */
    public function getDataForLocaleParentCodes()
    {
        return [
            ['pt_ST', 'pt_PT'],
            ['rus_Cyrl_BLR', null]
        ];
    }

    /**
     * @return array
     */
    public function getDataForLocaleAlternatives()
    {
        return [
            ['pt_ST', [
                'pt_Latn_ST',
                'pt_Latn_017',
                'pt_Latn_UN',
                'pt_Latn_002',
                'pt_Latn_001',
                'pt_ST',
                'pt_017',
                'pt_UN',
                'pt_002',
                'pt_001',
                'pt_EZ',
                'pt_150',
                'en_Latn_US',
                'en_Latn_021',
                'en_Latn_UN',
                'en_Latn_003',
                'en_Latn_019',
                'en_Latn_001',
                'en_US',
                'en_021',
                'en_UN',
                'en_003',
                'en_019',
                'en_001' ]
            ],
            ['rus_Cyrl_BLR', [
                'ru_Cyrl_BY',
                'ru_Cyrl_151',
                'ru_Cyrl_UN',
                'ru_Cyrl_150',
                'ru_Cyrl_001',
                'ru_BY',
                'ru_151',
                'ru_UN',
                'ru_150',
                'ru_001',
                'en_Latn_US',
                'en_Latn_021',
                'en_Latn_UN',
                'en_Latn_003',
                'en_Latn_019',
                'en_Latn_001',
                'en_US',
                'en_021',
                'en_UN',
                'en_003',
                'en_019',
                'en_001' ]
            ],
            ['be', [
                'be_Cyrl_BY',
                'be_Cyrl_151',
                'be_Cyrl_UN',
                'be_Cyrl_150',
                'be_Cyrl_001',
                'be_BY',
                'be_151',
                'be_UN',
                'be_150',
                'be_001',
                'en_Latn_US',
                'en_Latn_021',
                'en_Latn_UN',
                'en_Latn_003',
                'en_Latn_019',
                'en_Latn_001',
                'en_US',
                'en_021',
                'en_UN',
                'en_003',
                'en_019',
                'en_001' ]
            ]
        ];
    }
}
