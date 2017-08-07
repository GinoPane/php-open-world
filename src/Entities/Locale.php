<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;
use Closure;
use Exception;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

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
     * Source for likely-subtags data
     *
     * @var string
     */
    private static $likelySubtagsSourceUri = "likely.subtags.json";

    /**
     * Locale constructor
     *
     * @param Language $language
     * @param Script $script
     * @param Territory $territory
     */
    public function __construct(Language $language, Script $script = null, Territory $territory = null)
    {
        $this->language = $language;
        $this->script = $script;
        $this->territory = $territory;

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
        $localeString = str_replace('-', '_', $localeString);

        if ($subtags = self::getSubtags($localeString)) {
            list('locale' => $languageCode, 'script' => $scriptCode, 'territory' => $territoryCode) = $subtags;
        } else {
            @list($languageCode, $scriptCode, $territoryCode) = explode("_", $localeString, 3);
        }

        return new Locale(
            new Language($languageCode),
            $scriptCode ? new Script($scriptCode) : null,
            $territoryCode ? new Territory($territoryCode) : null
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
     * @return Script
     */
    public function getScript(): ?Script
    {
        return $this->script;
    }

    /**
     * @return Territory
     */
    public function getTerritory(): ?Territory
    {
        return $this->territory;
    }

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return void
     */
    public function assertCode(string $code, Closure $keyPredicate = null): void
    {
        // TODO: Implement assertCode() method.
    }

    /**
     * Returns Locale code. The pattern is language_script_territory
     *
     * @return string
     */
    public function getCode(): string
    {
        $code = $this->getLanguage()->getCode();

        if ($this->getScript()) {
            $code .= "_{$this->getScript()->getCode()}";
        }

        if ($this->getTerritory()) {
            $code .= "_{$this->getTerritory()->getCode()}";
        }

        return $code;
    }

    /**
     * Gets the array of relevant subtags
     *
     * @param string $languageString
     * @param Script|null $script
     * @param Territory|null $territory
     * @return array
     */
    protected static function getSubtags(string $languageString, Script $script = null, Territory $territory = null): array
    {
        $likelySubtags = self::getDataSourceLoader()->loadGeneral(self::$likelySubtagsSourceUri)->asArray();

        $subtagsKeysToCheck = [];

        if (is_null($script) && $territory) {
            $subtagsKeysToCheck[] = "{$languageString}_{$territory->getCode()}";
        } elseif ($script && is_null($territory)) {
            $subtagsKeysToCheck[] = "{$languageString}_{$script->getCode()}";
        }

        $subtagsKeysToCheck[] = $languageString;

        $subtags = [];

        foreach($subtagsKeysToCheck as $key) {
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
    protected function fillMissingSubtags(): void
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