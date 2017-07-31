<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

/**
 * Class Locale
 *
 * @package OpenWorld\Entities
 */
class Locale
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
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return Script
     */
    public function getScript(): Script
    {
        return $this->script;
    }

    /**
     * @return Territory
     */
    public function getTerritory(): Territory
    {
        return $this->territory;
    }
}