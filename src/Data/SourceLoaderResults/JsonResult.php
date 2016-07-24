<?php

namespace OpenWorld\Data\SourceLoaderResults;

use OpenWorld\Data\AbstractClasses\SourceLoaderResultAbstract;
use OpenWorld\Exceptions\InvalidContentException;
use stdClass;

/**
 * Class JsonResult
 * 
 * Provides access to loaded json data.
 * 
 * @package OpenWorld\Data\SourceLoaderResults
 */
class JsonResult extends SourceLoaderResultAbstract {

    /**
     * @inheritdoc
     */
    public function asString(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidContentException
     */
    public function asArray() : array
    {
        $data = @json_decode($this->content, true);

        if (!is_array($data)) {
            $this->throwInvalidContentError();
        }

        return $data;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidContentException
     */
    public function asObject() : stdClass
    {
        $data = @json_decode($this->content, false);

        if (!is_object($data)) {
            $this->throwInvalidContentError();
        }

        return $data;
    }

    /**
     * @inheritDoc
     *
     * Checks whether the passed JSON string is valid
     * @link  http://stackoverflow.com/questions/2583472/regex-to-validate-json
     *
     */
    public function isValid($content) : bool
    {
        $pcreRegex = '
          /
          (?(DEFINE)
             (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )    
             (?<boolean>   true | false | null )
             (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
             (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
             (?<pair>      \s* (?&string) \s* : (?&json)  )
             (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
             (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
          )
          \A (?&json) \Z
          /six   
        ';

        $matches = [];

        preg_match($pcreRegex, $content, $matches);

        return boolval($matches);
    }

    /**
     * Throws InvalidContentException with message provided
     * by json_last_error_msg() if has been one
     *
     * @throws InvalidContentException
     */
    private function throwInvalidContentError()
    {
        $message = (json_last_error() !== JSON_ERROR_NONE) ? json_last_error_msg() : '';

        throw new InvalidContentException($message);
    }
}