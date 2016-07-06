<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class BadDataFileContents
 *
 * An exception raised when a data file contains malformed data.
 *
 * @package OpenWorld\Exceptions
 */
class BadDataFileContents extends ExceptionAbstract
{
    protected $dataFilePath;

    protected $dataFileContents;

    /**
     * Initializes the instance.
     *
     * @param string $dataFilePath The path to the file with bad contents
     * @param string $dataFileContents The malformed of the file
     */
    public function __construct(string $dataFilePath, string $dataFileContents)
    {
        $this->dataFilePath = $dataFilePath;
        $this->dataFileContents = $dataFileContents;

        $message = "The file '$dataFilePath' contains malformed data";

        parent::__construct($message);
    }

    /**
     * Retrieves the path to the data file.
     *
     * @return string
     */
    public function getDataFilePath() : string
    {
        return $this->dataFilePath;
    }

    /**
     * Retrieves the malformed contents of the file.
     *
     * @return string
     */
    public function getDataFileContents() : string
    {
        return $this->dataFileContents;
    }
}
