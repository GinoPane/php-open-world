<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class DataFileNotReadableException
 *
 * An exception raised when a data file was not read.
 *
 * @package OpenWorld\Exceptions
 */
class DataFileNotReadableException extends ExceptionAbstract
{
    protected $dataFilePath;

    /**
     * Initializes the instance.
     *
     * @param string $dataFilePath The path to the unreadable file
     */
    public function __construct(string $dataFilePath)
    {
        $this->dataFilePath = $dataFilePath;
        $message = "Unable to read from the data file '$dataFilePath'";

        parent::__construct($message);
    }

    /**
     * Retrieves the path to the unreadable file.
     *
     * @return string
     */
    public function getDataFilePath() : string
    {
        return $this->dataFilePath;
    }
}
