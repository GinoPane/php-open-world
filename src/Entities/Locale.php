<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Exception;
use OpenWorld\OpenWorld;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;

/**
 * Class Locale
 *
 * @package OpenWorld\Entities
 */
class Locale extends EntityAbstract
{
    /**
     * Language entity
     *
     * @link https://en.wikipedia.org/wiki/ISO_639
     *
     * @var Language
     */
    private $language = null;

    /**
     * Script entity
     *
     * @link https://en.wikipedia.org/wiki/ISO_15924
     *
     * @var Script
     */
    private $script = null;

    /**
     * Territory entity
     *
     * @var Territory
     */
    private $territory = null;

    /**
     * Variant entity
     *
     * @var Variant
     */
    private $variant = null;

    /**
     * Source for locale aliases
     *
     * @var string
     */
    protected static $aliasSourceUri = 'language.alias.json';

    /**
     * Source for likely-subtags data
     *
     * @var string
     */
    private static $likelySubtagsSourceUri = "likely.subtags.json";

    /**
     * Source for parent locales
     *
     * @var string
     */
    private static $parentSourceUri = "locale.parents.json";

    /**
     * Locale constructor
     *
     * @param Language $language
     * @param Script $script
     * @param Territory $territory
     * @param Variant $variant
     */
    public function __construct(
        Language $language,
        Script $script = null,
        Territory $territory = null,
        Variant $variant = null
    ) {
        $this->language = $language;
        $this->script = $script;
        $this->territory = $territory;
        $this->variant = $variant;

        if (!$script || !$territory) {
            $this->fillMissingSubtags();
        }
    }

    /**
     * Creates Locale instance from locale string. String will be passed through likelySubtags verification.
     * Locale string should be formatted in a proper way: language[[_script][_territory]]
     *
     * @param string $localeString
     *
     * @return Locale
     */
    public static function fromString(string $localeString): Locale
    {
        $localeString = self::getCodeFromAlias(str_replace('-', '_', $localeString));

        if ($subtags = self::getSubtags($localeString)) {
            list('locale' => $languageCode, 'script' => $scriptCode, 'territory' => $territoryCode) = $subtags;
        } else {
            @list($languageCode, $scriptCode, $territoryCode, $variantCode) = explode("_", $localeString, 4);

            //If both codes are empty, then $scriptCode potentially might be actual territory or variant code instead
            //of the script code. It is done only for general solution, because in reality variants are defined only
            //when at least territory defined
            if (empty($variantCode) && empty($territoryCode) && !empty($scriptCode)) {
                if (Territory::codeIsLikelyValid($scriptCode)) {
                    $territoryCode = $scriptCode;
                    unset($scriptCode);
                } elseif (Variant::codeIsLikelyValid($scriptCode)) {
                    $variantCode = $scriptCode;
                    unset($scriptCode);
                }
            } elseif (empty($variantCode) && !empty($territoryCode)) {
                if (Territory::codeIsLikelyValid($scriptCode)) {
                    $variantCode = $territoryCode;
                    $territoryCode = $scriptCode;

                    unset($scriptCode);
                } elseif (Variant::codeIsLikelyValid($territoryCode)) {
                    $variantCode = $territoryCode;

                    unset($territoryCode);
                }
            }
        }

        return new Locale(
            new Language($languageCode),
            !empty($scriptCode) ? new Script($scriptCode) : null,
            !empty($territoryCode) ? new Territory($territoryCode) : null,
            !empty($variantCode) ? new Variant($variantCode) : null
        );
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return Script|null
     */
    public function getScript(): ?Script
    {
        return $this->script;
    }

    /**
     * @return Territory|null
     */
    public function getTerritory(): ?Territory
    {
        return $this->territory;
    }

    /**
     * @return Variant|null
     */
    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    /**
     * Returns Locale code. The pattern is language[_script[_territory[_variant]]
     *
     * @return string
     */
    public function getCode(): string
    {
        $codes = [
            $this->getLanguage()->getCode(),
            $this->getScript() ? $this->getScript()->getCode() : null,
            $this->getTerritory() ? $this->getTerritory()->getCode() : null,
            $this->getVariant() ? $this->getVariant()->getCode() : null,
        ];

        return implode("_", array_filter($codes));
    }

    /**
     * Gets the closest parent code
     *
     * @return null|string
     */
    public function getParentCode(): ?string
    {
        $parentCodes = self::getDataSourceLoader()->loadGeneral(self::$parentSourceUri);

        $languageCode = $this->getLanguage()->getCode();
        $scriptCode = $this->getScript() ? $this->getScript()->getCode() : null;
        $territoryCode = $this->getTerritory() ? $this->getTerritory()->getCode() : null;

        if (!empty($parentCodes["{$languageCode}_{$scriptCode}_{$territoryCode}"])) {
            return $parentCodes["{$languageCode}_{$scriptCode}_{$territoryCode}"];
        } elseif (!empty($parentCodes["{$languageCode}_{$scriptCode}"])) {
            return $parentCodes["{$languageCode}_{$scriptCode}"];
        } elseif (!empty($parentCodes["{$languageCode}_{$territoryCode}"])) {
            return $parentCodes["{$languageCode}_{$territoryCode}"];
        } elseif (!empty($parentCodes["{$languageCode}"])) {
            return $parentCodes["{$languageCode}"];
        }

        return null;
    }

    /**
     * Gets alternative locales for a given locale
     *
     * @param bool $includeFallback
     * @return array
     */
    public function getAlternativeCodes(bool $includeFallback = true): array
    {
        $alternatives = $this->buildAlternativeLocales($this);

        if ($includeFallback) {
            $alternatives = array_merge(
                $alternatives,
                $this->buildAlternativeLocales(Locale::fromString(OpenWorld::FALLBACK_LOCALE_CODE))
            );
        }

        return array_values(array_unique($alternatives));
    }

    /**
     * @param Locale $locale
     *
     * @return array
     */
    private function buildAlternativeLocales(Locale $locale)
    {
        $alternatives[] = $locale->getCode();

        $languageCode = $locale->getLanguage()->getCode();
        $scriptCode = $locale->getScript() ? $locale->getScript()->getCode() : null;
        $parentLocaleCode = $locale->getParentCode();

        if ($locale->getTerritory()) {
            $localeTerritoryCode = $locale->getTerritory()->getCode();

            $parentTerritoryCodes = array_merge(
                [$localeTerritoryCode],
                $locale->getTerritory()->getParentCodes()
            );

            foreach ($parentTerritoryCodes as $territoryCode) {
                $alternatives[] = implode("_", array_filter([
                    $languageCode,
                    $scriptCode,
                    $territoryCode
                ]));
            }

            if ($scriptCode) {
                if ($locale->getVariant()) {
                    $alternatives[] = "{$languageCode}_{$localeTerritoryCode}_{$locale->getVariant()->getCode()}";
                }

                foreach ($parentTerritoryCodes as $territoryCode) {
                    $alternatives[] = "{$languageCode}_{$territoryCode}";
                }
            }
        }

        if ($parentLocaleCode) {
            $alternatives += $this->buildAlternativeLocales(Locale::fromString($parentLocaleCode));
        }

        $alternatives[] = $languageCode;

        return $alternatives;
    }

    /**
     * Gets the array of relevant subtags
     *
     * @param string $languageString
     * @param Script|null $script
     * @param Territory|null $territory
     * @return array
     */
    private static function getSubtags(
        string $languageString,
        Script $script = null,
        Territory $territory = null
    ): array {
        $likelySubtags = self::getDataSourceLoader()->loadGeneral(self::$likelySubtagsSourceUri);

        $subtagsKeysToCheck = [];

        if (is_null($script) && $territory) {
            $subtagsKeysToCheck[] = "{$languageString}_{$territory->getCode()}";
        } elseif ($script && is_null($territory)) {
            $subtagsKeysToCheck[] = "{$languageString}_{$script->getCode()}";
        }

        $subtagsKeysToCheck[] = $languageString;

        $subtags = [];

        foreach ($subtagsKeysToCheck as $key) {
            if (!empty($likelySubtags[$key])) {
                $subtags = $likelySubtags[$key];

                break;
            }
        }

        return $subtags;
    }

    /**
     * Tries to fill missing subtags (script, territory) from likely-subtags data.
     * Missing subtags are not a big problem, so Exceptions would be converted to warnings.
     * Important: likely subtags may contain deprecated languages within full locale codes, so they would be
     * handled by Locale::createFromString
     *
     * @see Locale::createFromString
     * @return void
     */
    private function fillMissingSubtags(): void
    {
        try {
            $subtags = self::getSubtags($this->getLanguage()->getCode(), $this->getScript(), $this->getTerritory());

            if ($subtags) {
                if (is_null($this->getScript())) {
                    $this->script = new Script($subtags['script']);
                }

                if (is_null($this->getTerritory())) {
                    $this->territory = new Territory($subtags['territory']);
                }
            }
        } catch (Exception $exception) {
            trigger_error("Likely subtags could not be loaded: {$exception->getMessage()}", E_USER_WARNING);
        }
    }
}