<?php

use OpenWorld\Entities\Locale;
use OpenWorld\Entities\Language;

/**
 * Class LocaleTest
 */
class LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_territory_for_valid_identifier()
    {
        $locale = new Locale(new Language('rus'));

        var_dump($locale);

        $this->assertInstanceOf(Locale::class, $locale);
    }
}
