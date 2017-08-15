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
            ['aa_SAAHO', 'ssy_Latn_ER']
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
            ['pt_ST', array (
                0 => 'pt_Latn_ST',
                2 => 'pt_Latn_017',
                3 => 'pt_Latn_UN',
                4 => 'pt_Latn_002',
                5 => 'pt_Latn_001',
                6 => 'pt_ST',
                7 => 'pt_017',
                8 => 'pt_UN',
                9 => 'pt_002',
                10 => 'pt_001',
                11 => 'pt_EZ',
                13 => 'pt_150',
                15 => 'en_Latn_US',
                17 => 'en_Latn_021',
                18 => 'en_Latn_UN',
                19 => 'en_Latn_003',
                20 => 'en_Latn_019',
                21 => 'en_Latn_001',
                22 => 'en_US',
                23 => 'en_021',
                24 => 'en_UN',
                25 => 'en_003',
                26 => 'en_019',
                27 => 'en_001',
            )],
            ['rus_Cyrl_BLR', array (
                0 => 'ru_Cyrl_BY',
                2 => 'ru_Cyrl_151',
                3 => 'ru_Cyrl_UN',
                4 => 'ru_Cyrl_150',
                5 => 'ru_Cyrl_001',
                6 => 'ru_BY',
                7 => 'ru_151',
                8 => 'ru_UN',
                9 => 'ru_150',
                10 => 'ru_001',
                11 => 'en_Latn_US',
                13 => 'en_Latn_021',
                14 => 'en_Latn_UN',
                15 => 'en_Latn_003',
                16 => 'en_Latn_019',
                17 => 'en_Latn_001',
                18 => 'en_US',
                19 => 'en_021',
                20 => 'en_UN',
                21 => 'en_003',
                22 => 'en_019',
                23 => 'en_001',
            )],
            ['be', array (
                0 => 'be_Cyrl_BY',
                2 => 'be_Cyrl_151',
                3 => 'be_Cyrl_UN',
                4 => 'be_Cyrl_150',
                5 => 'be_Cyrl_001',
                6 => 'be_BY',
                7 => 'be_151',
                8 => 'be_UN',
                9 => 'be_150',
                10 => 'be_001',
                11 => 'en_Latn_US',
                13 => 'en_Latn_021',
                14 => 'en_Latn_UN',
                15 => 'en_Latn_003',
                16 => 'en_Latn_019',
                17 => 'en_Latn_001',
                18 => 'en_US',
                19 => 'en_021',
                20 => 'en_UN',
                21 => 'en_003',
                22 => 'en_019',
                23 => 'en_001',
            )]
        ];
    }
}
