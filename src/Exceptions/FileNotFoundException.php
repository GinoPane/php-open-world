<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class FileNotFoundException
 *
 * An exception raised when a data file was not read.
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class FileNotFoundException extends ExceptionAbstract
{
    /**
     * The path to the unreadable/nonexistent file
     *
     * @var string
     */
    protected $filePath;

    /**
     * Initializes the instance.
     *
     * @param string $filePath The path to the unreadable/nonexistent file
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $message = "Specified file does not exist or not readable: '$filePath'";

        parent::__construct($message);
    }

    /**
     * Retrieves the path to the unreadable file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
