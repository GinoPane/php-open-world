<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class FileNotValidException
 *
 * An exception raised when a data file was not read.
 *
 * @package OpenWorld\Exceptions
 */
class FileNotValidException extends ExceptionAbstract
{
    protected $filePath;

    /**
     * Initializes the instance.
     *
     * @param string $filePath The path to the unreadable file
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $message = "Specified file is not valid: '$filePath'";

        parent::__construct($message);
    }

    /**
     * Retrieves the path to the unreadable file.
     *
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->filePath;
    }
}
