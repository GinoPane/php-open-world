<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class BadDataFileContentsException
 *
 * An exception raised when a data file contains malformed data.
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class BadDataFileContentsException extends ExceptionAbstract
{
    /**
     * The path to the file with bad contents
     *
     * @var string
     */
    protected $dataFilePath;

    /**
     * The malformed of the file
     *
     * @var string
     */
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
    public function getDataFilePath(): string
    {
        return $this->dataFilePath;
    }

    /**
     * Retrieves the malformed contents of the file.
     *
     * @return string
     */
    public function getDataFileContents(): string
    {
        return $this->dataFileContents;
    }
}
