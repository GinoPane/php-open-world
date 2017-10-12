<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\SourceLoaderResults;

use OpenWorld\Exceptions\InvalidContentException;
use OpenWorld\Data\AbstractClasses\SourceLoaderResultAbstract;


/**
 * Class JsonResult
 *
 * Provides access to loaded json data.
 *
 * @package OpenWorld\Data\SourceLoaderResults
 */
class JsonResult extends SourceLoaderResultAbstract
{

    /**
     * Get result data as string.
     *
     * @return string
     */
    public function asString(): string
    {
        return $this->content;
    }

    /**
     * Get result data as array.
     *
     * @throws InvalidContentException
     */
    public function asArray(): array
    {
        $data = @json_decode($this->content, true);

        if (!is_array($data)) {
            $this->throwInvalidContentError();
        }

        return $data;
    }

    /**
     * Get result data as object.
     *
     * @inheritDoc
     *
     * @throws InvalidContentException
     */
    public function asObject()
    {
        $data = @json_decode($this->content, false);

        if (!is_object($data)) {
            $this->throwInvalidContentError();
        }

        return $data;
    }

    /**
     * Checks whether the passed JSON string is valid. RegExp testing has almost the same speed as @json_decode
     * with error check (generally faster for failed test, almost the same for passed).
     *
     * @param $content
     * @return bool
     *
     * @link  http://stackoverflow.com/questions/2583472/regex-to-validate-json
     */
    public function isValid($content): bool
    {
        $pcreRegex = '/
          (?(DEFINE)
             (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
             (?<boolean>   true | false | null )
             (?<string>    " ([^"\n\r\t\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
             (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
             (?<pair>      \s* (?&string) \s* : (?&json)  )
             (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
             (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
          )
          \A (?&json) \Z
          /six';

        preg_match($pcreRegex, $content, $matches);

        return (bool)($matches);
    }

    /**
     * Throws InvalidContentException with message provided
     * by json_last_error_msg() if has been one.
     *
     * @throws InvalidContentException
     */
    private function throwInvalidContentError()
    {
        $message = (json_last_error() !== JSON_ERROR_NONE) ? json_last_error_msg(): '';

        throw new InvalidContentException($message);
    }
}
